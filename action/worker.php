<?php

define(R, 0);

// Входной файл дочернего скрипта

include 'common.php';

$worker = new Worker();

do {
    $worker->run();
} while (!$worker->stop);