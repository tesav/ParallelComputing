<?php

define(R, 0);

// Входной файл дочернего скрипта

include 'common.php';

$begotten = new Begotten();

do {
    $begotten->run();
} while (!$begotten->stop);