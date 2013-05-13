<?php

defined('R') or die('Доступ запрещен !!!');

/**
 * Begotten
 *
 * Создает объект взаимодействия с порождающим скриптом
 *
 * @author Тесминецкий Александр <tesav@yandex.ru>
 */
class Begotten extends Communication implements IBegotten {

    /**
     * Ключ выхода
     *
     * @var boolean
     */
    public $stop = false;

    /**
     * Цвет вывода сообщений
     *
     * @var string
     */
    public $e_color = '#055';

    /**
     * Конструктор класса Begotten
     */
    public function __construct() {

        // Проверяем входные параметры
        !isset($_POST['id']) and die('Отсутствует идентификатор !');
        !isset($_POST['time']) and die('Отсутствует параметр времени !');

        //  Устанавливаем опорное время
        Begotten::$_timeAbout = microtime(true) - $_POST['time'];

        // Создаем потоки
        // Входящий: ~~
        // Исходящий: ~
        //
        parent::__construct($this->_prefix . $_POST['id'], $_POST['id']);

        //  Устанавливаем обработчик для возможности
        //  перехвата некритических ошибок и предупреждений
        set_error_handler(array('Begotten', 'error_handler'));

        //  Фиксируем и отправляем время старта
        $this->_report = Begotten::timer();
        $this->_send();
    }

    /**
     * Метод ожидает поступления, обрабатывает и отсылает данные
     */
    final public function run() {

        try {
            try {
                //  Получаем и обрабатываем
                $this->_report = $this->_waitAcceptHandler();
                //
            } catch (Exception_Begotten $e) {

                $this->_report = Begotten::decor($e, $this->e_color);
            }

            // Отправляем
            $this->_send();
            //
        } catch (Exception_Communication $e) {

            $this->_closeOut();
            echo $this->_exit();
        }
    }

    /**
     * Обрабатывает поступившие данные
     *
     * @return mixed
     */
    protected function _handler() {

        // Выходим
        if ($this->_report === Begotten::EXIT_)
            return $this->_exit();

        //  Читаем инструкцию
        return $this->_e(Begotten::_manual());
    }

    /**
     * Если уровень соответствующих ошибок установлен,
     * бросает исключение,
     * иначе возвращает false
     *
     * @param string
     * @param integer
     * @return boolean
     * @throws Exception_Begotten
     */
    protected function _e($text, $code = E_WARNING) {

        if (error_reporting() & $code) {
            throw new Exception_Begotten($text, $code);
        }
        return false;
    }

    /**
     * Метод останавливает работу скрипта
     *
     * @return float
     */
    private function _exit() {
        //
        $this->stop = true;
        //
        return Begotten::timer();
    }

    /**
     * Обработчик ошибок
     *
     * @param integer
     * @param string
     * @return boolean
     * @throws Exception_Begotten
     */
    public static function error_handler($code, $error) {

        if (error_reporting() & $code) {
            throw new Exception_Begotten($error, $code);
        }
        return true;
    }

}