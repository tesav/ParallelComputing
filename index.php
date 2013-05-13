<?php

define(R, 0);

//  Главный входной файл

if (version_compare(PHP_VERSION, '5.3.0', '<'))
    die('PHP версия должна быть не меньше: [ 5.3.0 ] , а у вас: [ ' . phpversion() . ' ].');


include 'action/common.php';
include 'test/TestMonteCarlo.php';

//
new TestMonteCarlo;