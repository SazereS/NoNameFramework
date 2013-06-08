<?php

// Строка запросы разбивается на части
$dir       = dirname($_SERVER['PHP_SELF']);
global $_url;
$_url      = str_replace(($dir == '/') ? '' : $dir, '', $_SERVER['REQUEST_URI']);
$_url_array = explode('/', '/' . trim($_url, '/'));
$last_param = explode('.', $_url_array[count($_url_array) - 1]);
if (count($last_param) > 1) {
    if (
            is_array(getResponse('accepted_formats')) AND
            in_array($last_param[count($last_param) - 1], getResponse('accepted_formats'))
    ) {
        setResponse('format', $last_param[count($last_param) - 1]);
        unset($last_param[count($last_param) - 1]);
        $_url_array[count($_url_array) - 1] = implode('.', $last_param);
        unset($last_param);
    }
}

$routes       = (loadConfig('routes'));
$route_length = count($_url_array) - 1;
if (isset($routes[trim($_url, '/')])) {
    $_url_array = explode('/', '/' . $routes[trim($_url, '/')]);
    goto make_response;
}
if (file_exists(APP_PATH . 'controllers/' . implode('/', array_slice($_url_array, 1, 2)) . '.php')) {
    goto make_response;
}

foreach ($routes as $key => $val) {
    if (count(explode('/', $key)) == $route_length) {
        $same_routes[$key] = $val;
    }
}

if (isset($same_routes))
    foreach ($same_routes as $key => $val) {
        $same         = 0;
        $route_params = array();
        $cur_route = explode('/', $key);
        foreach ($cur_route as $k => $v) {
            if (strlen($v) == 0 OR !($v['0'] == '{' AND $v[strlen($v) - 1] == '}')) {
                if ($v == $_url_array[$k + 1]) {
                    ++$same;
                } else {
                    break;
                }
            } else {
                $route_param_exploded = explode('|', trim($v, '{}'));
                if (count($route_param_exploded) > 1) {
                    $flag = true;
                    foreach ($route_param_exploded as $rk => $rv) {
                        if ($rk == 0) continue;
                        if(intval($rv) > 0){
                            if(strlen($_url_array[$k+1]) <= intval($rv)){
                                continue;
                            } else {
                                $flag = false;
                                break;
                            }
                        } elseif($rv[0] == '^' AND $rv[strlen($rv) - 1] == '$'){
                            $rv = '/' . $rv . '/';
                            if (!preg_match($rv, $_url_array[$k + 1])) {
                                $flag = false;
                            }
                        } else {
                            switch ($rv) {
                                case 'int':
                                case 'integer':
                                    if(!preg_match('/^([\d]+)$/', $_url_array[$k+1])){
                                        $flag = false;
                                    }
                                break;
                                case 'float':
                                case 'real':
                                case 'double':
                                    if(!preg_match('/^([\d]+[,.]{1}[\d]+)$/', $_url_array[$k+1])){
                                        $flag = false;
                                    }
                                break;
                                case 'str':
                                case 'string':
                                default:
                                break;
                            }
                        }
                    }
                    if($flag){
                        ++$same;
                        $route_params['{'.$route_param_exploded[0].'}'] = $_url_array[$k + 1];
                    }

                } else {
                    ++$same;
                    $route_params[$v] = $_url_array[$k + 1];
                }
            }
        }
        if ($same == $route_length) {
            $_url_array = explode('/', '/' . strtr($val, $route_params));
            break;
        }
    }

make_response: // Метка для перехода
// Получаем экшн и контроллер из запроса
$_response['controller'] = ((count($_url_array) > 1) AND ($_url_array[1])) ? getRealName($_url_array[1]) : 'index';
$_response['action']     = ((count($_url_array) > 2) AND ($_url_array[2])) ? getRealName($_url_array[2]) : 'index';

// Получаем остальные параметры запроса
if (count($_url_array) > 3) {
    for ($i = 3; $i < count($_url_array) - 1; $i+=2) {
        $_params[$_url_array[$i]] = ($_url_array[$i + 1]) ? $_url_array[$i + 1] : false;
    }
} else {
    $_params = array();
}