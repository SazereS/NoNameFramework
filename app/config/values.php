<?php

// В этом файле устанавливаются значения по-умолчанию
// для данного сайта

return array(
    'default_language' => 'et',    // For translater
    'default_locale'   => 'en_US', // For system values, like names of days, etc...
    'default_timezone' => 'Europe/Moscow',
    'encode'          => 'UTF8',
    'page404'         => false,
    'ignoring_errors' => array(
        'Undefined index:',
        'Undefined variable:',
    ),
    'autoload' => array(

    ),
    'modules' => array(
        //'mysql'
        'sqlite'
    )
);
