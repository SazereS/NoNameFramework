<?php

// В этом файле устанавливаются значения по-умолчанию
// для данного сайта

return array(
    'encode'          => 'UTF-8',
    'ignoring_errors' => array(
        'Undefined index:',
        'Undefined variable:',
    ),
    'acl' =>array(
        'active' => true,
        'default_group' => 'user',
        'deny_handler' => 'accessError'
    )
);
