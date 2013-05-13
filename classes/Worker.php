<?php

defined('R') or die('Доступ запрещен !!!');

/**
 *  Worker
 *
 *  Расширяет возможности объекта порожденного скрипта,
 *  позволяя осмысленно отвечать на запросы.
 *
 *  @author Тесминецкий Александр <tesav@yandex.ru>
 */
class Worker extends Begotten {

    /**
     * Подключенные файлы
     *
     * @var array
     */
    private $_files = array();

    /**
     * Подключает файл
     *
     * @return integer
     */
    public function remote_include() {

        if (in_array($this->_report['include'], $this->_files))
            return 1;

        $this->_files[] = $this->_report['include'];

        try {
            return include $file = DOCROOT . $this->_report['include'] . EXT;
        } catch (Exception_Begotten $e) {
            return $this->_e("Файл: ''{$file}'' не найден !");
        }
    }

    /**
     * Возвращает результат вызова
     * запрошенной функции или метод класса
     *
     * @return mixed
     */
    public function remote_function() {

        $func = $this->_report['function'];

        //  Если нельзя вызвать
        if (!is_callable($func)) {

            if (is_string($func))
                return $this->_e("Функция: ''{$func}'' не найдена !");

            if ((is_array($func)))
                return $this->_e("Метод: ''{$func[1]}'' не найден !");
        }

        //  Если аргументов нет создаем пустой массив
        isset($this->_report['arg']) ?
                        $arg = $this->_report['arg'] :
                        $arg = array();

        //  Если не массив,
        //  значит аргументы есть !
        //  Помещаем их в массив
        //
        !is_array($arg) and $arg = array($arg);

        //  Возвращаем результат
        return call_user_func_array($func, $arg);
    }

    /**
     * Возвращает результат вызова
     * запрошенного метода
     *
     * @return mixed
     */
    public function remote_method() {

        //
        $this->_class_exists() or
                $this->_e("Класс: ''{$this->_report['class']}'' не подключен !");

        //  Если не задан метод
        $this->_report['method'] or
                $this->_e('Отсутствует имя метода !');

        //  Помещаем в массив и добавляем ключ 'function'
        $this->_report['function'] = array($this->_report['class'], $this->_report['method']);

        //  Отдаем на дальнейшую обработку
        return $this->remote_function();
    }

    /**
     * Обработчик входных данных
     *
     * @return mixed
     */
    protected function _handler() {

        //  Если данные пришли в виде стоки
        if (is_string($this->_report))

        //  Отдаем строковому обработчику
            return $this->_stringHandler();

        //  Если в виде массива
        if (is_array($this->_report))

        //  Отдаем обработчику массива
            return $this->_arrayHandler();

        //  Если обработать не удалось
        //  ! Важно ! дать возможность
        //  ! выйти !
        //
        return parent::_handler();
    }

    /**
     * Обработчик строковых данных
     *
     * @return mixed
     */
    protected function _stringHandler() {

        //  Исключаем обращение к защищенному свойству
        $prop = str_replace('_', '', $this->_report);

        //  Пытаемся отдать значение запрашиваемого свойства
        //try {
        return $this->$prop;
        //} catch (Exception_Begotten $e) {
        //    return $this->_e("Свойство: ''{$prop}'' не существует");
        //}
    }

    /**
     * Вызывается автоматически,
     * если свойство не существует
     *
     * @return mixed
     */
    public function __get($name) {
        return $this->_e("Свойство: ''{$name}'' не существует");
    }

    /**
     * Обработчик данных,
     * пришедших в виде массива
     *
     * @return mixed
     */
    protected function _arrayHandler() {

        foreach ($this->_report as $key => $value) {

            // Ищем метод по ключу массива
            if (method_exists($this, $method = 'remote_' . strtolower($key)))
                return $this->$method();
        }

        //  Если обработать не удалось
        //  Читаем инструкцию
        return $this->_manual();
    }

    /**
     * Проверяет, существует ли класс
     *
     * @return boolean
     */
    protected function _class_exists() {

        isset($this->_report['class']) or
                $this->_e('Отсутствует ключ: "class" !');

        if (@class_exists($this->_report['class']))
            return true;
        return false;
    }

}