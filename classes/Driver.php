<?php

defined('R') or die('Доступ запрещен !!!');

/**
 * Driver
 *
 * @author Тесминецкий Александр <tesav@yandex.ru>
 */
class Driver {

    /**
     * Папка с драйверами
     *
     * @var string
     */
    public static $dir = 'drivers';

    /**
     * Подключенные драйверы
     *
     * @var array
     */
    private static $_mem = array();

    /**
     *
     * @param string
     * @param string
     * @return object
     * @throws Exception_Driver
     */
    public static function load($driver, $id) {

        //
        if (!$driver or !$id)
        //
            throw new Exception_Driver('Отсутствуют параметры загрузки !', 1);


        if (!in_array($driver, self::$_mem)) {
            //
            if (!file_exists($file = LIB . self::$dir . DIRECTORY_SEPARATOR . $driver . EXT))
            //
                throw new Exception_Driver("Файл: ''{$file}'' не найден !", 1);
            //
            include $file;
            //
            self::$_mem[] = $driver;
        }
        //
        return new $driver($id);
    }

}

/**
 *
 */
class Exception_Driver extends Exception_Communication {

}