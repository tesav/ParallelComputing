<?php

defined('R') or die('Доступ запрещен !!!');

/**
 * Exception_Begotten
 *
 * @author Тесминецкий Александр <tesav@yandex.ru>
 */
class Exception_Begotten extends Exception {

    /**
     *
     * @param Exception $e
     * @return string
     */
    public static function text(Exception $e) {

        if ($e->code == E_WARNING)
            return $e->getMessage();

        $trace = $e->getTrace();
        return sprintf('<< "%s" [ %s ] Файл: %s В строке: %s'
                        , $e->getMessage()
                        , $e->code
                        , $trace[0]['file']
                        , $trace[0]['line']);
    }

    /**
     *
     * @return string
     */
    public function __toString() {
        return $this->text($this);
    }

}