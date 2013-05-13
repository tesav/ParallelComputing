<?php

defined('R') or die('Доступ запрещен !!!');

/**
 * Драйвер File
 *
 * @author Тесминецкий Александр <tesav@yandex.ru>
 */
class File implements IRw {

    /**
     * Свойство класса
     *
     * @var string
     */
    private $_file = '';

    /**
     * Конструктор класса File
     *
     * @param string
     * @throws Exception_Driver
     */
    public function __construct($file) {
        if (empty($file))
            throw new Exception_Driver('Отсутствует имя файла', 1);
        $this->_file = DOCROOT . 'tmp' . DIRECTORY_SEPARATOR . $file;
    }

    /**
     * Метод класса
     *
     * @return string
     */
    public function read() {

        // Читаем
        $data = @file_get_contents($this->_file);

        // Очищаем
        $data and @fclose(@fopen($this->_file, 'w'));

        // Отдаем
        return $data;
    }

    /**
     * Метод класса
     *
     * @param string
     * @return boolean
     */
    public function write($data) {

        return (bool) @file_put_contents($this->_file, $data);
    }

    /**
     * Метод класса
     *
     * @return boolean
     */
    public function close() {

        do {
            clearstatcache();

            if (!file_exists($this->_file))
                return true;
            //
            @unlink($this->_file) or usleep(10);
            //
        } while (true);
    }

}