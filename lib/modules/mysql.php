<?php

/**
 * Алиас mysqli_query()
 *
 * @param string $q
 * @return query_result
 */
function query($q) {
    global $_db;
    $result = mysqli_query($_db, $q);
    if (!mysqli_errno($_db)) {
        return $result;
    } else {
        debugTrace();
        die('<strong>Ошибка MySQL!</strong> ' . mysqli_error($_db));
    }
}

/**
 * Алиас mysqli_fetch_assoc()
 *
 * @param query_result $q
 * @return array
 */
function fetch($q) {
    try {
        return mysqli_fetch_assoc($q);
    } catch (ErrorException $e) {
        global $_db;
        debugTrace();
        die('<strong>Ошибка MySQL!</strong>' . mysqli_error($_db));
    }
}

/**
 * Алиас mysqli_num_rows()
 *
 * @param query_result $q
 * @return integer
 */
function num($q) {
    try {
        return mysqli_num_rows($q);
    } catch (ErrorException $e) {
        global $_db;
        debugTrace();
        die('<strong>Ошибка MySQL!</strong>' . mysqli_error($_db));
    }
}

function lastAutoincrement(){
        global $_db;
        return mysqli_insert_id($_db);
}


$db  = loadConfig('mysql');
global $_db;

$_db = mysqli_connect(
        $db['dbHost'], $db['dbUser'], $db['dbPass']
);

mysqli_select_db($_db, $db['dbName']);
query('SET NAMES ' . ((getValue('encode')) ? getValue('encode') : 'utf8'));

unset($db);