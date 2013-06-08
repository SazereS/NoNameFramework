<?php

// Обработчик ошибок
function handleError($errno, $errstr, $errfile, $errline, array $errcontext) {
    global $_values;
    // error was suppressed with the @-operator
    if (0 === error_reporting()) {
        return false;
    }

    foreach ($_values['ignoring_errors'] as $value) {
        if (strpos($errstr, $value) !== false) {
            return;
        }
    }
    if (APP_DEBUG) {
        debugTrace();
        throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
    }
}

set_error_handler('handleError');