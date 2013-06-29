<?php

// Объявление глобальных переменных
global $_values;
global $_response;
global $_view;

// Загрузка конфигов
$_values   = loadConfig('values');
$_response = loadConfig('response');
$_view = array();