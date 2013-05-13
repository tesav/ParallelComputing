<?php

defined('R') or die('Доступ запрещен !!!');

/**
 * Process
 *
 * Cоздает объект взаимодействия с порожденным скриптом
 *
 * @author Тесминецкий Александр <tesav@yandex.ru>
 */
class Process extends Communication implements IProcess {

    /**
     * Путь к файлу вызываемого скрипта
     *
     * @var string
     */
    public $script = '';

    /**
     * Время ожидания ответа
     * запуска вызываемого скрипта
     *
     * @var float or integer
     */
    public $delayStart = .5;

    /**
     * Время вызова
     *
     * @var float or integer
     */
    public $call = null;

    /**
     * Цвет вывода ошибок
     * вызываемого скрипта
     *
     * @var string
     */
    public $e_color = 'grey';

    /**
     * Режима приема данных
     *
     * @var boolean
     */
    private $_receive = false;

    /**
     * Идентификатор соединения
     *
     * @var resource
     */
    private $_id = null;

    /**
     * Время старта вызываемого скрипта
     *
     * @var float or integer
     */
    private $_start = null;

    /**
     * Время останова вызываемого скрипта
     *
     * @var float or integer
     */
    private $_stop = null;

    /**
     * Конструктор класса Process
     *
     * @param string  путь к файлу вызываемого скрипта
     *  относительно корневой директории
     *  (приоритет выше указанного в конфигурационном файле).
     * @throws Exception_Process
     */
    public function __construct($script = '') {

        //  Запускаем таймер
        Process::timer();

        //  Загружаем конфиг
        $this->_loadConfig('process');

        //  Если скрипт указан,
        //  переназначаем
        if ($script)
            $this->script = trim($script);

        //
        if (!$this->script)
            throw new Exception_Process('Отсутствует имя вызываемого скрипта !', 1);

        //  Вызываем и фиксируем время
        $this->call = $this->_callScript();

        // Создаем потоки
        // Входящий: ~
        // Исходящий: ~~
        //
       parent::__construct($this->_prefix . $this->_id, $this->_prefix . $this->_prefix . $this->_id);

        //   Если вызываемый скрипт не ответил, ловим исключение
        try {
            $this->_start = $this->_waitAcceptHandler($this->delayStart);
        } catch (Exception_Communication $e) {

            //  Бросаем исключение с информацией о причине сбоя
            throw new Exception_Process("Вызываемый скрипт не запустился: {$e->getMessage()}" . Process::decor($this->_dCS(), $this->e_color), 1);
        }
    }

    /**
     * Метод отсылает запрос и возвращает полученный обработанный результат.
     * Если: $wait == false, -- отсылает запрос и возвращает true.
     *
     * @param mixed
     * @param boolean
     * @return mixed
     * @throws Exception_Process
     */
    final public function request($data, $wait = true) {

        if ($this->stopped())
            throw new Exception_Process("Невозможно отправить данные скрипт остановлен: {$this->_stop}", 1);

        try {
            return $this->_request($data, $wait);
        } catch (Exception $e) {

            $this->_closeOut();
            throw new Exception_Process("Ошибка запроса: {$e->getMessage()}" . Process::decor($this->_dCS(), $this->e_color), 1);
        }
    }

    /**
     * Метод возвращает ответ скрипта.
     * Если $wait == false, -- возвращает ответ без ожидания или false.
     *
     * @param boolean
     * @return mixed
     * @throws Exception_Process
     */
    final public function response($wait = true) {

        if ($this->stopped())
            throw new Exception_Process("Невозможно получить данные скрипт остановлен: {$this->_stop}", 1);

        try {
            return $this->_response($wait);
        } catch (Exception $e) {

            $this->_closeOut();
            throw new Exception_Process("Ошибка ответа: {$e->getMessage()}" . Process::decor($this->_dCS(), $this->e_color), 1);
        }
    }

    /**
     * Метод возвращает время старта вызываемого скрипта
     *
     * @return float or integer
     */
    public function start() {
        return $this->_start;
    }

    /**
     * Метод останавливает вызываемый скрипт и (или) возвращает время останоа
     *
     * @return float or integer
     */
    public function stop() {
        return $this->_stop ? $this->_stop : $this->_stop = $this->request(Process::EXIT_);
    }

    /**
     * Метод возвращает все, что посылается в выходной поток в вызванном скрипте
     * ! Обращаться к методу только после останова вызванного скрипта
     *
     * @return string
     */
    public function displayCallScript() {
        stream_set_blocking($this->_id, true);
        return $this->_dCS();
    }

    /**
     * Метод сообщает осановлен ли скрипт.
     *
     * @return boolean
     */
    public function stopped() {
        return (bool) $this->_stop ? $this->_stop : $this->_stop = $this->_dCS();
    }

    /**
     * Возвращает необработанные данные
     *
     * @return mixed
     */
    protected function _handler() {
        return $this->_report;
    }

    /**
     *
     * @param mixed
     * @param boolean
     * @return mixed
     * @throws Exception_Process
     */
    private function _request($data, $wait) {

        //  Если строка, убираем пробелы
        is_string($data) and $data = trim($data);

        if (!$data)
            throw new Exception_Process('Запрос не может быть пустым !', 1);

        //  Запрещаем посылать несколько запросов подряд
        if ($this->_receive == true)
            throw new Exception_Process('Предыдущий запрос еще не обработан !', 1);
        $this->_receive = true;

        //  Устанавливаем данные
        $this->_report = $data;

        // Посылаем запрос
        $this->_send();

        //  Если необходимо дождаться ответа,
        //  возвращаем ответ,
        //  иначе true

        return $wait ? $this->_response() : true;
    }

    /**
     *
     * @param boolean
     * @return boolean or mixed
     */
    private function _response($wait = true) {

        //  Если данные приняты, возвращаем
        if ($this->accepted())
            return $this->_report;

        //  Если ждать
        if ($wait) {

            //  Разрешаем следующий запрос
            $this->_receive = false;

            //  Ждем и возвращаем обработанный результат
            return $this->_waitAcceptHandler();
        }

        //  Если удалось получить данные
        if ($this->_accept()) {

            //  Разрешаем следующий запрос
            $this->_receive = false;

            //  Возвращаем обработанный результат
            return $this->_handler();
        }
        //  Получить не удалось
        return false;
    }

    /**
     *
     * @return float
     * @throws Exception_Process
     */
    private function _callScript() {

        $this->_id = fsockopen($_SERVER['HTTP_HOST'], $_SERVER['SERVER_PORT']);

        if (!is_resource($this->_id))
            throw new Exception_Process('Ошибка открытия сокета', 2);

        stream_set_blocking($this->_id, false);

        $data = http_build_query(array(
            //
            //  _id автоматически приводится к строковому типу
            //  при добавлении префикса
            //
            'id' => $this->_prefix . $this->_id,
            'time' => $time = Process::timer(),
                ));

        fwrite($this->_id, "POST /" . $this->script . EXT .
                " HTTP/1.1\r\nHost: " . $_SERVER['HTTP_HOST'] . "\r\n" .
                "Content-Type: application/x-www-form-urlencoded\r\n" .
                "Content-Length: " . strlen($data) . "\r\n" .
                "Connection: close\r\n\r\n$data"
        );

        return $time;
    }

    /**
     *
     * @return string
     */
    private function _dCS() {

        $temp = explode("\n\r", stream_get_contents($this->_id));
        unset($temp[0]);
        return trim(implode('', $temp));
    }

    /**
     *
     * @return string
     */
    public function __toString() {
        return (string) $this->_id;
    }

    /**
     *
     */
    public function __destruct() {
        parent::__destruct();

        // Закрываем соединение
        pclose($this->_id);
    }

}

/**
 * Exception_Process
 */
class Exception_Process extends Exception_Communication {/* * */
}