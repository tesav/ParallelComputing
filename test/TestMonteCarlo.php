<?php

/**
 *
 */
class TestMonteCarlo {

    /**
     * Все значения Pi
     *
     * @var array
     */
    public static $pi = array();

    /**
     * Все значения Tau
     *
     * @var array
     */
    public static $tau = array();

    /**
     * Массив процессов
     *
     * @var array
     */
    private static $_pr = array();

    /**
     * Содержит информацию о попытках получения ответа
     *
     * @var array
     */
    private static $_attempt = array();

    /**
     *
     */
    public function __construct() {

        $config = include 'configTestMonteCarlo.php';

        $min = isset($config['minPoints']) ? (int) $config['minPoints'] : 1;
        $max = isset($config['maxPoints']) ? (int) $config['maxPoints'] : 1;

        ($min < 1 or $min > $max) and die('Проверь максимальное и минимальное количество точек !');

        $numProc = isset($config['numberProcesses']) ? (int) $config['numberProcesses'] : 0;

        $arr = array();

        //  Создаем процессы и даем задание

        for ($i = 0; $i < $numProc; $i++)
            $arr[] = self::testGo(mt_rand($min, $max));


        //  Опрашиваем через неравные промежутки времени

        while ($arr) {
            foreach ($arr as $key => $value) {

                //
                usleep(mt_rand(rand(0, $max - $min), $max));

                if (self::_answer($arr[$key])) {
                    unset($arr[$key]);
                }
            }
        }


        if (self::$pi) {
            echo '<br />*********Pi********* = ' . Communication::decor(M_PI, 'blue');
            echo '<br />Среднее значение Pi** = ' . Communication::decor(array_sum(self::$pi) / count(self::$pi), '#456');
            echo '<br />Среднее значение Tau* = ' . Communication::decor(array_sum(self::$tau) / count(self::$tau), '#456');
        }
    }

    /**
     *
     * @param integer
     * @return object Process
     */
    public static function testGo($points = 1) {

        //  Всегда >= 0
        $points = abs($points);

        $pr = new Process();

        $pr->request(array(
            'include' => 'test/testClasses/MonteCarlo',
        ));


        $pr->request(array(
            'class' => 'MonteCarlo',
            'method' => 'castPoint',
            'arg' => $points,
                ), false);

        echo"<br />Процесс id: ''{$pr}''";

        echo"<br />Вызываем: " . $pr->call;
        echo"<br />Стартовал : " . $pr->start();

        echo '<hr>';

        return $pr;
    }

    /**
     *
     * @param object Process
     * @return boolean
     */
    private static function _answer($pr) {


        $id = (string) $pr;

        if (isset(self::$_attempt[$id]['i']))
            self::$_attempt[$id]['i']++;
        else {
            self::$_attempt[$id]['i'] = 1;
            self::$_attempt[$id]['t'] = '';
        }


        self::$_attempt[$id]['t'] .= Communication::decor("<br />''{$pr}'' >> " . self::$_attempt[$id]['i'] . " попытка получить ответ: " . Process::timer(), 'grey');


        if (!$points = $pr->response(false)) {
            self::$_attempt[$id]['t'] .= Communication::decor(" << ''{$pr}'' ответил: работаю...");
            return false;
        }

        $echo = self::$_attempt[$id]['t'];

        $echo .= '<br /><br />Бросил первую точку: ' . $pr->request(array(
                    // Можно и так
                    'method' => 'getStart',
                    'class' => 'MonteCarlo',
                ));

        $echo .= "<br />Бросил последнюю: " . $pr->request(array(
                    'class' => 'MonteCarlo',
                    'method' => 'getStop',
                ));


        $echo .= "<br />Всего брошено: {$points}";

        $echo .= '<br />Pi = ' . self::$pi[] = $pr->request(array(
            'class' => 'MonteCarlo',
            'method' => 'getPi'
                ));

        $echo .= '<br />Tau = ' . self::$tau[] = $pr->request(array(
            'class' => 'MonteCarlo',
            'method' => 'getTau'
                ));

        $echo .= "<br />Скрипт ''{$pr}'' завершился: " . $pr->stop();

        unset(self::$_attempt[$id]);

        echo Communication::decor($echo, 'green') . '<hr>';


        return true;
    }

}
