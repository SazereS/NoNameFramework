<?php

/**
 * Содержит параметры для авторизации через сессии
 *
 */
global $_sessionAuth;

/**
 * Алиас session_start()
 *
 */
function sessionStart() {
    session_start();
}

/**
 * Пытается авторизовать пользователя, используя
 * новые данные
 *
 */
function sessionSetAuth($id, $pass) {
    global $_sessionAuth;
    preventCashing();
    require_once(LIB_PATH . 'models/basemodel.php');
    $stack = fetchRow($_sessionAuth['table'], $_sessionAuth['id'] . ' = \'' . $id . '\'');
    if (!$stack) {
        $_SESSION['auth'] = false;
        return;
    }
    $_sessionAuth['entered_password'] = $_sessionAuth['hashing_function']($pass . (($_sessionAuth['salt']) ? $stack[$_sessionAuth['salt']] : ''));
    if ($stack[$_sessionAuth['password']] == $_sessionAuth['entered_password']) {
        $_SESSION['auth'] = $stack;
    } else {
        $_SESSION['auth'] = false;
    }
}

/**
 * Пытается авторизовать пользователя, используя
 * данные, сохраненные в сессии. Обновляет данные
 * в сессии
 *
 */
function sessionAuth() {
    sessionStart();
    if (!checkAuth()) {
        return false;
    }
    global $_sessionAuth;
    preventCashing();
    require_once(LIB_PATH . 'models/basemodel.php');
    $stack = fetchRow($_sessionAuth['table'], $_sessionAuth['id'] . ' = \'' . $_SESSION['auth'][$_sessionAuth['id']] . '\'');
    if (!$stack) {
        $_SESSION['auth']                 = false;
        return;
    }
    $_sessionAuth['entered_password'] = $_SESSION['auth'][$_sessionAuth['password']];
    if ($stack[$_sessionAuth['password']] == $_sessionAuth['entered_password']) {
        $_SESSION['auth'] = $stack;
    } else {
        $_SESSION['auth'] = false;
    }
}

/**
 * Устанавливает таблицу, хранящую данные для авторизации
 *
 */
function setSessionAuthTable($table) {
    global $_sessionAuth;
    $_sessionAuth['table'] = $table;
}

/**
 * Устанавливает имя колонки, в которой хранится имя пользователя
 *
 */
function setSessionAuthIdCol($id) {
    global $_sessionAuth;
    $_sessionAuth['id'] = $id;
}

/**
 * Устанавливает имя колонки, в которой хранится пароль пользователя
 *
 */
function setSessionAuthPasswordCol($pass) {
    global $_sessionAuth;
    $_sessionAuth['password'] = $pass;
}

/**
 * Устанавливает имя колонки, в которой хранится соль для пароля
 *
 */
function setSessionAuthSaltCol($salt = false) {
    global $_sessionAuth;
    $_sessionAuth['salt'] = $salt;
}

/**
 * Устанавливает функцию для шифрования (хэширования) пароля
 *
 */
function setSessionAuthHashing($hashing_function = 'md5') {
    global $_sessionAuth;
    $_sessionAuth['hashing_function'] = $hashing_function;
}

/**
 * Проверяет, авторизован ли пользователь
 *
 */
function checkAuth() {
    return ($_SESSION['auth']) ? true : false;
}

/**
 * Возвращает массив данных с информацие об авторизованном
 * пользователе, либо один из его элементов
 *
 */
function getAuth($row = false) {
    if (checkAuth()) {
        return $row ? $_SESSION['auth'][$row] : $_SESSION['auth'];
    } else {
        return false;
    }
}

/**
 * Сбрасывает авторизацию пользователя
 *
 */
function sessionLogOut() {
    unset($_SESSION['auth']);
}