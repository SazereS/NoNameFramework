<?php

if(getValue('autoload')) foreach(getValue('autoload') as $foo){
    loadFunction($foo);
}

if(getValue('modules')) foreach(getValue('modules') as $module){
    $module_file = LIB_PATH . 'modules/' . $module . '.php';
    if(!file_exists($module_file)){
        debugTrace();
        die('<b>Ошибка!</b> Невозможно загрузить модуль <b>' . $module);
    } else {
        require_once($module_file);
    }
}