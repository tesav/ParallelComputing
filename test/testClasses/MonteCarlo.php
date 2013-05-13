<?php

/**
 * MonteCarlo
 *
 * @author Тесминецкий Александр <tesav@yandex.ru>
 */
class MonteCarlo {

    /**
     * Количество брошенных точек
     *
     * @var integer
     */
    private static $_p = null;

    /**
     * Количество точек, попавших в круг
     *
     * @var integer
     */
    private static $_pIn = null;

    /**
     * Время
     *
     * @var float
     */
    private static $_start = null;

    /**
     * Время
     *
     * @var float
     */
    private static $_stop = null;

    /**
     *
     * @param integer
     * @return float
     */
    public static function castPoint($n = 1) {

        //
        $n = (int) $n;

        //  Для повышения уникальности задаваемых координат
        //  определяем максимально допустимое генерируемое число
        $max = mt_getrandmax();

        //
        self::$_start = Communication::timer();

        //
        for (; self::$_p <= $n - 1; self::$_p++) {

            // Получаем x и y, равные псевдослучайным значениям от 0 до 1
            //
            $x = mt_rand(1, $max) / $max;
            $y = mt_rand(1, $max) / $max;

            //  Если точка принадлежит кругу,
            //  добавляем
            //
            self::$_pIn += ($x * $x + $y * $y) <= 1;
        }

        //
        self::$_stop = Communication::timer();

        return self::$_p;
    }

    /**
     *
     * @return float
     */
    public static function getStart() {

        return self::$_start;
    }

    /**
     *
     * @return float
     */
    public static function getStop() {

        return self::$_stop;
    }

    /**
     *
     * @return float
     */
    public static function getPi() {

        //  Если брошена хоть одна точка,
        //  вычисляем значение Pi

        if (self::$_p > 0)
        //
            return (self::$_pIn / self::$_p) * 4;

        return -1;
    }

    /**
     *
     * @return float
     */
    public static function getTau() {


        //  Если брошена хоть одна точка,
        //  Вычисляем значение Tay

        if (self::$_p > 0)
        //
            return ((self::$_pIn / self::$_p) * 2 ) * 4;

        return -1;
    }

}