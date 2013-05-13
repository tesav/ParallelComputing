<?php

/**
 *
 */
define(R, 0);


include '../action/common.php';

$i = 1;

do {

    $process = new Process('action/worker');

    echo '<br />Вызов функции с аргументами разных типов: <br />';

    $process->request(array(
        'function' => 'array_reverse',
        'arg' => array(array('!', 'Мир ', 'Привет '), true),
    ));

    //
    print_r($process->response());
    //
    echo '<br />Результат сохраняется в свойстве "_report"  до следующего запроса<br />';
    //
    print_r($process->response());

    echo '<br />Вызов функции с аргументом массивом: ';

    echo $process->request(array(
        'function' => 'implode',
        'arg' => array($process->response()),
    ));

    echo '<br />Вызов функции без аргументов: ';

    echo $process->request(array(
        'function' => 'microtime',
    ));

    echo '<br />Вызов функции с одним аргументом: ';

    echo $process->request(array(
        'function' => 'microtime',
        'arg' => true,
    ));

    echo '<br />Вызов функции с тремя аргументами: ';

    $process->request(array(
        'function' => 'str_replace',
        'arg' => array(':', '<>', '1:2:3:4:5:6:7:8:9'),
    ));

    print_r($process->response());


    echo '<br />Вызов мeтодов: ';

    echo $process->request(array(
        'class' => 'Communication',
        'method' => 'decor',
        'arg' => array('Привет Мир !', 'yellow'),
    ));

    echo '<br />Результат "include" : ';

    $process->request(array(
        'include' => 'test/testClasses/Test',
    ));

    if ($process->response() === 1)
    //
        echo 'ok';

    echo '<br />Повторно "include" : ';

    $process->request(array(
        'include' => 'test/testClasses/Test',
    ));

    if ($process->response() === 1)
    //
        echo 'ok';


    echo '<br />Если метод или функция выводит сообщение на экран,
        увидеть его можно, вызвав метод "displayCallScript" : ';

    $process->request(array(
        'class' => 'Test',
        'method' => 'calc',
        'arg' => array(25, 4),
    ));

    //
    $process->stop();
    //
    echo $process->displayCallScript();

    echo '<br />Затрачено: ';

    echo $process->stop() . 'сек.';
} while (--$i > 0);



