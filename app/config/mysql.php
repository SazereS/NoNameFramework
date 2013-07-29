<?php

// В этом файле устанавливаются параметры для подключения
// к MySQL базе сайта когда Debug-режим отключен, либо когда
// файл app/config/debug/mysql.php отсутствует
return array(
    'dbHost' => 'localhost',
    'dbUser' => 'root',
    'dbPass' => '',
    'dbName' => 'production',
    'time_zone' => '+04:00'
);