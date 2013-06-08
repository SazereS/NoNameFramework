<?php

$db  = loadConfig('mysql');
global $_db;

$_db = mysql_connect(
        $db['dbHost'], $db['dbUser'], $db['dbPass']
);

mysql_select_db($db['dbName']);
mysql_query('SET NAMES ' . ((getValue('encode')) ? getValue('encode') : 'UTF-8'));

unset($db);