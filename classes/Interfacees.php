<?php

defined('R') or die('Доступ запрещен !!!');

/**
 * Interfaces
 *
 *  @author Тесминецкий Александр <tesav@yandex.ru>
 */

/**
 * interface IProcess
 */
interface IProcess {

    /**
     *
     */
    public function start();

    /**
     *
     */
    public function stop();

    /**
     *
     * @param mixed
     */
    public function request($data);

    /**
     *
     */
    public function response();

    /**
     *
     */
    public function __tostring();
}

/**
 * interface IBegotten
 */
interface IBegotten {

    /**
     *
     */
    public function run();
}

/**
 * interface IRw
 */
interface IRw {

    /**
     *
     */
    public function read();

    /**
     *
     * @param string
     */
    public function write($data);
}

