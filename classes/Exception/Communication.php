<?php

defined('R') or die('Доступ запрещен !!!');

/**
 * Exception_Communication
 *
 * @author Тесминецкий Александр <tesav@yandex.ru>
 */
class Exception_Communication extends Exception {

    /**
     * Метод класса
     *
     * @param Exception $e
     */
    public static function handler(Exception $e) {

        if (error_reporting() & $e->getCode()) {

            echo Communication::decor($e);
        }
        exit(1);
    }

    /**
     * Метод класса
     * 
     * @param Exception $e
     * @return string
     */
    public static function text(Exception $e) {
        return sprintf('<br />%s [ %s ] "%s" ', get_class($e), $e->getCode(), /** strip_tags */ ($e->getMessage()));
    }

    /**
     * Метод класса
     * @return string
     */
    public function __toString() {
        return $this->text($this);
    }

}
