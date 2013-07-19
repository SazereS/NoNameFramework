<?php

/**
 * Алиас sqlite_query()
 *
 * @param string $q
 * @return query_result
 */
function query($q) {
    global $_db;
    $result = $_db->query($q);
    if (!$_db->lastErrorCode()) {
        return $result;
    } else {
        debugTrace();
        die('<strong>Ошибка SQLite!</strong> ' . $_db->lastErrorMsg());
    }
}

/**
 * Алиас sqlite_fetch_array()
 *
 * @param query_result $q
 * @return array
 */
function fetch(SQLite3Result $q) {
    try {
        return $q->fetchArray(SQLITE3_ASSOC);
    } catch (ErrorException $e) {
        global $_db;
        debugTrace();
        die('<strong>Ошибка SQLite!</strong>' . $e->getMessage());
    }
}

/**
 * Алиас mysqli_num_rows()
 *
 * @param query_result $q
 * @return integer
 */
function num(SQLite3Result $q) {
    try {
        return $q->numColumns();
    } catch (ErrorException $e) {
        global $_db;
        debugTrace();
        die('<strong>Ошибка SQLite!</strong>' . $e->getMessage());
    }
}

function lastAutoincrement(){
        global $_db;
        return $_db->lastInsertRowID();
}

$db  = loadConfig('sqlite');
global $_db;

$_db = new SQLite3(
        $db['path'] . DIRECTORY_SEPARATOR . $db['name'] . '.sqlite'
);

unset($db);