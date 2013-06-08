<?php

////////////////////////////////////////////////////////////////////////////////////////
//  Debuggers
////////////////////////////////////////////////////////////////////////////////////////

/**
 * Выводит бэктрейс
 * @return string Информация о строке и т.д.
 */
function debugTrace() {
    global $_response;
    global $_params;
    global $_values;
    if (!APP_DEBUG)
        return;
    $trc = debug_backtrace();
    unset($trc[0]);
    foreach ($trc as $key => $value) {
        printf(
                //'<br />' .
                '(' . $key . ') ' .
                'Файл: <b>%s</b>,' . "<br />&nbsp;&nbsp;&nbsp;&nbsp;" .
                'строка: <b>%s</b>,' . "<br />&nbsp;&nbsp;&nbsp;&nbsp;" .
                'метод: <b>%s</b>,' . "<br />&nbsp;&nbsp;&nbsp;&nbsp;" .
                'аргументы метода: <b>(%s)</b>.<br />', $value['file'], $value['line'], $value['function'], implode($value['args'], ', ')
        );
    }
    echo '<pre>';
    echo '$_response: ';
    var_dump($_response);
    echo '$_params: ';
    var_dump($_params);
    echo '</pre>';
}

/**
 * Возвращает бэктрейс, попутно сохраняя его в глобальный массив.
 * Не прерывает выполнение скрипта
 *
 * @global array $_trace
 * @return string
 */
function silentTrace() {
    global $_trace;
    return $_trace[] = debug_backtrace();
}

////////////////////////////////////////////////////////////////////////////////////////
//  Starters
////////////////////////////////////////////////////////////////////////////////////////

/**
 * Запускает экшн и (если есть) инит контроллера, передает экшну массив $_view
 *
 * @global array $_response
 * @global array $_params
 * @global array $_view
 * @return void
 */
function startAction() {
    global $_response;
    global $_params;
    global $_view;
    $init = APP_PATH . 'controllers/' . $_response['controller'] . '/_init.php';
    if (file_exists($init)) {
        include_once($init);
    }
    silentTrace();
    $file = APP_PATH . 'controllers/' . $_response['controller'] . '/' . $_response['action'] . 'Action.php';
    if (!file_exists($file)) {
        debugTrace();
        die('<strong>Ошибка!</strong> Контроллер <b>"' . $_response['controller'] . '"</b> не существует, либо не имеет экшна  <b>"' .
                $_response['action'] . '"</b>!');
    } else {
        include_once($file);
    }
}

/**
 * Подключает вью и передает ему массив $_view
 *
 * @global array $_view
 * @param string $view
 * @return void
 */
function startView($view) {
    global $_view;
    $file = APP_PATH . 'views/' . $view . '.phtml';
    if (file_exists($file)) {
        include($file);
    } else {
        debugTrace();
        die('<strong>Ошибка!</strong> Вью <strong>' . $file . '</strong> не существует!');
    }
}

/**
 * Подключает вью для определенного экшна
 *
 * @param string $controller
 * @param string $action
 * @return void
 */
function startActionView($controller, $action) {
    $file = 'scripts/' . $controller . '/' . $action;
    startView($file);
}

////////////////////////////////////////////////////////////////////////////////////////
//  Loaders
////////////////////////////////////////////////////////////////////////////////////////

/**
 * Загружает файл из набора функций
 *
 * @param string $foo
 * @return void
 */
function loadFunction($foo) {
    $file = LIB_PATH . 'functions/' . $foo . '.php';
    if (file_exists($file)) {
        include($file);
    } else {
        debugTrace();
        die('<strong>Ошибка!</strong> Сета функций <strong>' . $file . '</strong> не существует!');
    }
}

/**
 * Загружает вью из папки "app/views/custom"
 *
 * @param string $view
 * @return void
 */
function loadCustomView($view) {
    startView('custom/' . $view);
}

/**
 * Загружает phtml лайаут из папки "app/views/layout", по умолчанию "default.phtml"
 *
 * @param string $layout = 'default'
 * @return void
 */
function loadLayout($layout = 'default') {
    global $_response;
    global $_values;
    global $_view;
    switch ($_response['format']) {
        case 'json':
            header('Content-Type: application/json');
            startAction();
            print(json_encode(json_fix_cyr($_response['result']), JSON_PRETTY_PRINT));
            break;
        case 'xml':
            header('Content-Type: application/xml; charset=' . $_values['encode']);
            startAction();
            print(xml_encode($_response['result']));
            break;
        case 'html':
        default:
            $file = APP_PATH . 'views/layout/' . $layout . '.phtml';
            if (!file_exists($file)) {
                debugTrace();
                die('<strong>Ошибка!</strong> Лайаута "' . $layout . '" не существует!');
            }
            include($file);
            break;
    }
}

/**
 * Загружает конфиг и возвращает его содержимое
 *
 * @param string $config
 * @return array
 */
function loadConfig($config) {
    if(APP_DEBUG AND file_exists(APP_PATH . 'config/debug/' . $config . '.php')){
        return require(APP_PATH . 'config/debug/' . $config . '.php');
    } elseif(file_exists(APP_PATH . 'config/' . $config . '.php')){
        return require(APP_PATH . 'config/' . $config . '.php');
    } else {
        if(APP_DEBUG){
            debugTrace();
            die('<strong>Ошибка!</strong> Конфиг <strong>' . $config . '</strong> не найден');
        }
        return false;
    }
}

/**
 * Подключает модель и базовую модель
 *
 * @param string $model Имя модели
 * @return void
 */
function loadModel($model) {

    if (file_exists(LIB_PATH . 'models/basemodel.php')) {
        require_once(LIB_PATH . 'models/basemodel.php');
    } else {
        debugTrace();
        die('<strong>Ошибка!</strong> Базовая модель <strong>library/models/basemodel.php</strong> не найдена!');
    }
    if (file_exists($file = APP_PATH . 'models/' . $model . '.php')) {
        return include_once($file);
    } else {
        debugTrace();
        die('<strong>Ошибка!</strong> Сета функций <strong>' . $file . '</strong> не существует!');
    }
}

////////////////////////////////////////////////////////////////////////////////////////
//  Setters
////////////////////////////////////////////////////////////////////////////////////////

/**
 * Устанавливает значение параметра ответа (Response)
 *
 * @param string $key
 * @param mixed $val
 * @return void
 */
function setResponse($key, $val) {
    global $_response;
    $_response[$key] = $val;
}

/**
 * Устанавливает содержимое ответа, для вывода в других типах
 *
 * @param mixed $val Устанавливаемое значение
 * @return void
 */
function setResult($val) {
    global $_response;
    $_response['result'] = $val;
}

/**
 * Устанавливает значение параметра запроса (Param)
 *
 * @param string $key
 * @param mixed $val
 * @return void
 */
function setParam($key, $val) {
    global $_params;
    $_params[$key] = $val;
}

////////////////////////////////////////////////////////////////////////////////////////
//  Getters
////////////////////////////////////////////////////////////////////////////////////////

/**
 * Возвращает имя экшна или контроллера в camelCase
 *
 * @param string $url_name
 * @return string In camelCase
 */
function getRealName($url_name) {
    $url_name = str_replace('.', '', $url_name);
    $url      = explode('-', $url_name);
    if (count($url) < 2) {
        return $url_name;
    }
    foreach ($url as $key => $val) {
        if ($key > 0) {
            $val[0]   = strtoupper($val[0]);
        }
        $result[] = $val;
    }
    return implode($result);
}

/**
 * Возвращает массив данных, переданных методом POST,
 * либо один из элементов этого массива
 *
 * @param string $key = FALSE
 * @return mixed
 */
function getPost($key = FALSE) {
    return isPost() ? (($key) ? $_POST[$key] : $_POST) : false;
}

/**
 * Возвращает значение параметра ответа
 *
 * @param string $key
 * @return mixed
 */
function getResponse($key) {
    global $_response;
    return $_response[$key];
}

/**
 * Возвращает содержимое ответа, предназначенного для вывода в другом типе
 *
 * @return mixed
 */
function getResult() {
    global $_response;
    return $_response['result'];
}

/**
 * Возвращает значение параметра, переданного из адресной строки (param)
 *
 * @param string $key
 * @return mixed
 */
function getParam($key) {
    global $_params;
    return $_params[$key];
}

function getValue($key){
    global $_values;
    return $_values[$key];
}

////////////////////////////////////////////////////////////////////////////////////////
//  Helpers
////////////////////////////////////////////////////////////////////////////////////////

/**
 * Выводит содержимое вью на экран
 *
 * @global array $_response
 * @return void
 */
function content() {
    global $_response;
    startActionView($_response['controller'], $_response['action']);
}

/**
 * Алиас mysql_query()
 *
 * @param string $q
 * @return query_result
 */
function query($q) {
    global $_db;
    $result = mysql_query($q, $_db);
    if (!mysql_errno()) {
        return $result;
    } else {
        debugTrace();
        die('<strong>Ошибка MySQL!</strong> ' . mysql_error());
    }
}

/**
 * Алиас mysql_fetch_array()
 *
 * @param query_result $q
 * @return array
 */
function fetch($q) {
    try {
        return mysql_fetch_array($q);
    } catch (ErrorException $e) {
        debugTrace();
        die('<strong>Ошибка MySQL!</strong>' . mysql_error());
    }
}

/**
 * Алиас mysql_num_rows()
 *
 * @param query_result $q
 * @return integer
 */
function num($q) {
    try {
        return mysql_num_rows($q);
    } catch (ErrorException $e) {
        debugTrace();
        die('<strong>Ошибка MySQL!</strong>' . mysql_error());
    }
}

/**
 * Обрезает текст до разделителя (по умолчанию "<cut />")
 *
 * @param string $text
 * @param string $delimiter = '<cut />'
 * @return string
 */
function cutForPreview($text, $delimiter = '<cut />') {
    $r = explode($delimiter, $text);
    return $r[0];
}

/**
 * Возвращает адрес от корня сайта
 *
 * @param string $name
 * @return string
 */
function baseUrl($name) {
    return str_replace('//', '/', str_replace('\\', '/', dirname($_SERVER['PHP_SELF'])) . '/' . $name);
}

/**
 * Генерирует URL для папки 'public'
 *
 * @param string $url
 * @return string
 */
function publicUrl($url) {
    return baseUrl('public/' . $url);
}

/**
 * Проверяет число на четность
 *
 * @param integer $n
 * @return boolean
 */
function isEven($n) {
    if (($n % 2) == 0) {
        return true;
    } else {
        return false;
    }
}

/**
 * Перенаправляет пользователя на заданный адрес
 *
 * @param string $to = ''
 * @return void
 */
function redirect($to = '') {
    header('Location: ' . baseUrl($to));
}

/**
 * Устанавливает маркер пропуска кэширования для
 * следующего запроса к базе
 *
 * @return void
 */
function preventCashing() {
    global $_dont_cashing;
    $_dont_cashing = true;
}

/**
 * Проверяет наличие данных, переданных методом POST
 *
 * @return boolean
 */
function isPost() {
    return !empty($_POST);
}

/**
 * Собирает тайтл страницы из массива, либо устанавливает
 * новый уровень тайтла
 *
 * @global array $_response
 * @param string $title = FALSE
 * @return string
 */
function headTitle($title = false) {
    global $_response;
    if ($title) {
        $_response['title'][] = $title;
    }
    return implode($_response['title_delimeter'], $_response['title']);
}

/**
 * Устанавливает разделитель для элементов тайтла
 *
 * @global array $_response
 * @param string $delim
 * @return string
 */
function headTitleDelimeter($delim = false) {
    global $_response;
    if ($delim) {
        $_response['title_delimeter'] = $delim;
    }
    return $_response['title_delimeter'];
}

/**
 * Возвращает массив в виде XML структуры
 *
 * @param array $array
 * @param string $root = 'xmldata'
 * @param integer $level = 0
 * @return string
 */
function xml_encode($array, $root = 'xmldata', $level = 0) {
    $res = '';
    if ($level == 0) {
        $res .= '<?xml version="1.0"?>' . "\n";
    }
    if ($root) {
        $res .= '<' . $root . '>' . "\n";
    }
    if ($array)
        foreach ($array as $k => $v) {
            if (is_int($k)) {
                $k = 'v' . $k;
            }
            if (is_array($v)) {
                $res .= str_repeat('  ', $level);
                $res .= '<' . $k . '>' . "\n";
                $res .= xml_encode($v, false, $level + 1);
                $res .= '</' . $k . '>' . "\n";
            } else {
                $res .= str_repeat('  ', $level);
                $res .= '<' . $k . '>';
                $res .= $v;
                $res .= '</' . $k . '>' . "\n";
            }
        }
    if ($root) {
        $res .= '</' . $root . '>';
    }
    return $res;
}

/**
 * Изменяет кодировку текста на UTF-8
 *
 * @global array $_values
 * @param mixed $var
 * @return mixed
 */
function json_fix_cyr($var) {
    global $_values;
    if (is_array($var)) {
        $new = array();
        foreach ($var as $k => $v) {
            $new[json_fix_cyr($k)] = json_fix_cyr($v);
        }
        $var                   = $new;
    } elseif (is_object($var)) {
        $vars = get_object_vars($var);
        foreach ($vars as $m => $v) {
            $var->$m = json_fix_cyr($v);
        }
    } elseif (is_string($var)) {
        $var = iconv($_values['encode'], 'utf-8', $var);
    }
    return $var;
}