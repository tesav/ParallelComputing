<?php

/**
 * 
 */

define(R, 0);


include '../action/common.php';

$time = microtime(true);

$i = 3;

do {

    $process = new Process('action/begotten');

    echo 'вызов: ' . $process->call . ' сек.<br />';
    echo 'старт: ' . $process->start() . '<br />';

    //
    echo 'true = ' . $process->request(true) . '<br />';
    echo '1 = ' . $process->request(1) . '<br />';
    echo '555 = ' . $process->request(555) . '<br />';
    echo '555.555 = ' . $process->request(555.555) . '<br />';
    echo '"1" = ' . $process->request('1') . '<br />';

    //
    //echo 'false = ' . $process->request(false) . '<br />';
    //echo '0 = ' . $process->request(0) . '<br />';
    //echo '"0" = ' . $process->request('0') . '<br />';
    //echo 'Пустая строка = ' . $process->request('') . '<br />';
    //echo 'Пробел = ' . $process->request('  ') . '<br />';
    //
    echo 'id : ' . $process . '<br />';
    //
    echo 'Свойство "data" = ' . $process->request('data') . '<br />';
    echo 'Свойство "color" = ' . $process->request('color') . '<br />';
    echo 'Свойство "stop" = ' . $process->request('stop') . '<br />';

    echo 'Несуществующее свойство "abra" = ' . $process->request('abra') . '<br />';

    //
    $process->stop();
    echo 'Затрачено: ' . $process->stop() . ' cek<br />';
    //
} while (--$i > 0);



$d = microtime(true) - $time;

echo 'Затрачено всего: ' . $d . ' cek<br />';

//
echo implode('<br />', glob(DOCROOT . 'tmp/' . '*')) . '<br />';

