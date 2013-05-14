<?php

// Включаем или отключаем дебаг (влияет на бэктрейс и загрузку конфигов)
define('APP_DEBUG', TRUE);
// Определяются папки приложения
define('APP_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'app' . DIRECTORY_SEPARATOR);
define('LIB_PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR . 'lib' . DIRECTORY_SEPARATOR);
// Подключаются все файлы, необходимые для работы фреймворка
require_once(LIB_PATH . 'include.php');
// Запускаем экшн
startAction();
// Загружаем лайаут
loadLayout($_response['layout']);
