<?php

/**
 *
 */

define(R, 0);


include '../action/common.php';

$time = microtime(true);

$i = 1;

do {
    $process = new Process('action/worker');

    //
    echo 'Скрипт вызван: ' . $process->call . ' сек.<br />';
    echo 'Скрипт ответил: ' . $process->start() . ' сек.<br />';
    echo 'Время отклика: ' . ($process->start() - $process->call) . ' сек.<br />';
    //
    echo 'true = ' . $process->request(true) . '<br />';
    echo '1 = ' . $process->request(1) . '<br />';
    echo '555 = ' . $process->request(555) . '<br />';
    echo '555.555 = ' . $process->request(555.555) . '<br />';
    echo '"1" = ' . $process->request('1') . '<br />';

    //
    // echo 'false = ' . $process->request(false) . '<br />';
    // echo '0 = ' . $process->request(0) . '<br />';
    // echo '"0" = ' . $process->request('0') . '<br />';
    // echo 'Пустая строка = ' . $process->request('') . '<br />';
    // echo 'Пробел = ' . $process->request('  ') . '<br />';
    //
    echo 'id: ' . $process . '<br />';
    //
    echo 'Свойство "_report" = ' . $process->request('_report') . '<br />';
    echo 'Свойство "stop" = ' . $process->request('stop') . '<br />';
    echo $process->accepted() . '<br />';

    echo 'Несуществующее свойство "abra" = ' . $process->request('abra') . '<br />';

    // Останавливаем вызванный скрипт

    $process->stop();
    //
    echo 'Затрачено: ' . $process->stop() . ' сек.<br />';
    //
} while (--$i > 0);



$d = microtime(true) - $time;

echo 'Всего затрачено: ' . $d . ' cek.<br />';

//
echo implode('<br />', glob(DOCROOT . 'tmp/' . '*')) . '<br />';

