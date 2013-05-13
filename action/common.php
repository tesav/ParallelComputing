<?php

defined('R') or die('Доступ запрещен !!!');

// Общий входной файл

define('DOCROOT', realpath(dirname(__DIR__)) . DIRECTORY_SEPARATOR);

define('LIB', DOCROOT . 'classes' . DIRECTORY_SEPARATOR);

define('EXT', '.php');


error_reporting(E_ALL | E_STRICT);
//error_reporting(0);

set_time_limit(0);

include LIB . 'Interfacees' . EXT;

spl_autoload_register(function ($class) {

            $class = str_replace('_', DIRECTORY_SEPARATOR, $class);

            include LIB . $class . EXT;
        });
