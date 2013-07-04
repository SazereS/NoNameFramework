<?php

// Объявление глобальных переменных
global $_values;
global $_response;
global $_view;
global $_relations;

// Загрузка конфигов
$_values   = loadConfig('values');
$_response = loadConfig('response');
$_view = array();
$_relations = loadConfig('relations');