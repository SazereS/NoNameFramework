<?php

// В этом файле выполняется начальная инициализация
// модулей и параметров сайта

function initDb() {
    $db  = loadConfig('mysql');
    global $_db;
    global $_values;
    $_db = mysql_connect(
            $db['dbHost'], $db['dbUser'], $db['dbPass']
    );
    mysql_select_db($db['dbName']);
    mysql_query('SET NAMES ' . (($_values['encode']) ? $_values['encode'] : 'UTF-8'));
}

//initDb();

function initHeadTitle() {
    headTitleDelimeter(' - ');
    headTitle('Framework');
}

initHeadTitle();
