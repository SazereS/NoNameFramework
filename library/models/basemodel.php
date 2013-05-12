<?php

/**
 * Конвертирует результат запроса в ассоциативный массив
 *
 * @param query_result $res
 * @return array
 */
function resourceToArray($res) {
    if ($res)
        while ($e = fetch($res)) {
            $r[] = $e;
        } else {
        $r = false;
    }
    return $r;
}

/**
 * Возвращает все строки, подходящие под условия
 *
 * @global boolean $_require_again
 * @global boolean $_dont_cashing
 * @global array $_cash
 * @param string $table
 * @param string $where
 * @param string $sort
 * @param string $limit
 * @return array
 */
function fetchAll($table, $where = false, $sort = false, $limit = false) {
    global $_require_again;
    global $_dont_cashing;
    global $_cash;
    $where = ($where) ? ' WHERE ' . $where : '';
    $sort  = ($sort) ? ' ORDER BY ' . $sort : '';
    $limit = ($limit) ? ' LIMIT ' . $limit : '';
    $q     = 'SELECT * FROM `' . $table . '`' . $where . $sort . $limit;
    $res   = query($q);
    if ($_require_again OR (empty($_cash['queries'][$q]))) {
        $_require_again = false;
        return $_cash['queries'][$q] = resourceToArray($res);
    } elseif ($_dont_cashing) {
        $_dont_cashing = false;
        return resourceToArray($res);
    } else {
        return $_cash['queries'][$q];
    }
}

/**
 * Возвращает только одну - первую - строку, подходящую под условия
 *
 * @global boolean $_require_again
 * @global array $_cash
 * @param string $table
 * @param string $where
 * @param string $sort
 * @return array
 */
function fetchRow($table, $where = false, $sort = false) {
    global $_require_again;
    global $_cash;
    $where = ($where) ? ' WHERE ' . $where : '';
    $sort  = ($sort) ? ' ORDER BY ' . $sort : '';
    $q     = 'SELECT * FROM `' . $table . '`' . $where . $sort . ' LIMIT 1';
    $res   = query($q);
    if ($_require_again OR (empty($_cash['queries'][$q]))) {
        $_require_again       = false;
        return $_cash['queries'][$q] = fetch($res);
    } else {
        return $_cash['queries'][$q];
    }
}

/**
 * Вставляет данные в новую строку в таблице
 *
 * @param string $table
 * @param array $values
 * @return boolean
 */
function insertRow($table, $values = array()) {
    foreach ($values as $k => $v) {
        $cols[] = $k;
        $vals[] = $v;
    }
    return query('INSERT INTO `' . $table . '` (`' . implode('`, `', $cols) . '`) VALUES (\'' . implode('\',\'', $vals) . '\')');
}

/**
 * Обновляет данные в строке
 *
 * @param string $table
 * @param string $where
 * @param array $values
 * @return boolean
 */
function updateRow($table, $where = false, $values = array()) {
    $where = ($where) ? ' WHERE ' . $where : '';
    foreach ($values as $k => $v) {
        $vals[] = '`' . $k . '`' . '=\'' . $v . '\'';
    }
    return query('UPDATE ' . $table . ' SET ' . implode(', ', $vals) . $where);
}

/**
 * Удаляет подходящую под условия строку (строки) из таблицы
 *
 * @param string $table
 * @param string $where
 * @return query_result
 */
function deleteRow($table, $where = false) {
    $where = ($where) ? ' WHERE ' . $where : '';
    return query('DELETE FROM `' . $table . '`' . $where);
}

/**
 * Возвращает количество строк, подходящих под условие
 *
 * @param string $table
 * @param string $where
 * @param string $sort
 * @param string $limit
 * @return integer
 */
function countRows($table, $where = false, $sort = false, $limit = false) {
    $where = ($where) ? ' WHERE ' . $where : '';
    $sort  = ($sort) ? ' ORDER BY ' . $sort : '';
    $limit = ($limit) ? ' LIMIT ' . $limit : '';
    $q     = 'SELECT * FROM `' . $table . '`' . $where . $sort . $limit;
    return num(query($q));
}