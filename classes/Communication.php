<?php

defined('R') or die('Доступ запрещен !!!');

/**
 * Абстрактный класс Communication
 *
 * Oбщий для дочерних объектов порождающего и порожденного скрипта
 *
 * @author Тесминецкий Александр <tesav@yandex.ru>
 */
abstract class Communication {
    //
    // Команда выхода

    const EXIT_ = -1;

    /**
     * Используемый драйвер
     *
     * @var string
     */
    public $driver = 'file';

    /**
     * Время ожидания ответа
     * в процессе выполнения
     *
     * @varr float or integer
     */
    public $delay = .5;

    /**
     * Префикс, добавляемый к именам потоков
     *
     * @var string
     */
    protected $_prefix = '~';

    /**
     * Свойство для обмена информацией
     * между скриптами
     *
     * @var mixed
     */
    protected $_report = null;

    /**
     * Входной поток
     *
     * @var object instanceof IRw
     */
    private $_in;

    /**
     * Выходной поток
     *
     * @var object instanceof IRw
     */
    private $_out;

    /**
     * Регистрирует факт принятия данных
     *
     * @var boolean
     */
    private $_accepted = false;

    /**
     * Опорное время
     *
     * @var float
     */
    protected static $_timeAbout = null;

    /**
     * Округление результата таймера
     *
     * @var integer
     */
    private static $_round = 2;

    /**
     * Конструктор класса Communication
     *
     * @param string
     * @param string
     * @throws Exception_Communication
     */
    public function __construct($in, $out) {

        // Получаем общие настройки
        $this->_loadConfig('common');

        isset($this->round) and self::$_round = abs((int) $this->round);

        $this->_in = Driver::load($this->driver, $in);

        set_exception_handler(array('Exception_Communication', 'handler'));

        if (!$this->_in instanceof IRw)
            throw new Exception_Communication('Несовместимый формат!', 1);

        $this->_out = Driver::load($this->driver, $out);
    }

    /**
     * Метод возвращает время, прошедшее с момента первого вызова
     *
     * @return float or integer
     */
    public static function timer() {

        //  Если опорное время установленно,
        //  возвращаем округленную разницу
        //  между нынешним и относительным
        if (self::$_timeAbout)
            return round(microtime(true) - self::$_timeAbout, self::$_round);

        //  Устанавливаем опорное время
        self::$_timeAbout = microtime(true);

        //
        return null;
    }

    /**
     * Если данные были приняты возвращает true
     *
     * @return boolean
     */
    public function accepted() {
        return $this->_accepted;
    }

    /**
     * Устанавливает значения свойств
     * в значения одноименных ключей массива
     * конфигурационного файла
     *
     * @param string
     */
    protected function _loadConfig($config) {

        $arr = include DOCROOT . 'config' . DIRECTORY_SEPARATOR . $config . EXT;

        foreach ($arr as $key => $value) {

            $this->$key = $value;
        }
    }

    /**
     *  Данный метод должен быть определён в дочернем классе
     *
     *  Метод должен обрабатывать данные, находящиеся свойстве "$_report"
     *  и возвращать результат.
     *
     *  @return mixed
     */
    abstract protected function _handler();

    /**
     * Метод считывает данные с входного потока
     *
     * @return boolean
     * @throws Exception_Communication
     */
    protected function _accept() {

        try {

            //
            $this->_report = unserialize($data = $this->_in->read());

            //
            return $this->_accepted = (bool) $data;
            //
        } catch (Exception_Driver $e) {
            //
            throw new Exception_Communication(get_class($this) . ' -- Ошибка чтения: ' . $e->getMessage(), 1);
        }
    }

    /**
     * Метод считывает данные с входного потока
     * с ожиданием $delay или $this->delay
     *
     * @param integer
     * @return mixed
     * @throws Exception_Communication
     */
    protected function _waitAcceptHandler($delay = null) {

        $delay = $delay ? abs($delay) : $this->delay;

        $wait = microtime(true) + $delay;

        // Ждем поступления данных
        do {

            if ($this->_accept() or usleep(10)) {
                return $this->_handler();
            }

            //
        } while ($wait > microtime(true));
        //
        throw new Exception_Communication(get_class($this) . " -- Истекло время ожидания ответа: ''{$delay}'' сек. !", 1);
    }

    /**
     * Метод помещает данные в выходной поток
     *
     * @return boolean
     * @throws Exception_Communication
     */
    protected function _send() {


        try {

            //
            $this->_out->write(serialize($this->_report));
            //
            $this->_accepted = false;
            //
            $this->_report = null;

            //
            return true;
            //
        } catch (Exception_Driver $e) {
            //
            throw new Exception_Communication(get_class($this) . ' -- Ошибка записи: ' . $e->getMessage(), 1);
        }
    }

    /**
     * Метод возвращает строку помещенную в контейнер span
     *
     * @param string
     * @param string
     * @return string
     */
    public static function decor($string, $color = 'red') {
        //
        return '<span style="color:' . $color . '" >' . nl2br($string) . '</span>';
    }

    /**
     * Возвращает ссылку на инструкцию
     *
     * @return string
     */
    protected static function _manual() {
        //
        return '<a href="http://' . $_SERVER['HTTP_HOST'] .
                '/manual/index.php">Читать инструкцию >></a>';
    }

    /**
     * Закрывает выходной поток
     */
    protected function _closeOut() {

        is_object($this->_out) and
                $this->_out->close();
    }

    /**
     * Закрывает входной поток
     */
    public function __destruct() {

        is_object($this->_in) and
                $this->_in->close();
    }

}