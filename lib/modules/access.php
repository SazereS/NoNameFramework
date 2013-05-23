<?php

// Модуль ACL
$acl = getValue('acl');
if($acl['active'] === true){
    $rules = loadConfig('access');
    $temp_group = $group = (getResponse('group')) ? getResponse('group') : $acl['default_group'];
    while (true){
        foreach(array('deny','access',) as $ruletype){
            if(is_array($rules[$temp_group][$ruletype])){
                foreach ($rules[$temp_group][$ruletype] as $k => $v) {
                    if(is_int($k)){
                        $rules[$temp_group][$ruletype][$v] = true;
                        unset($rules[$temp_group][$ruletype][$k]);
                    } elseif (is_array($v)) {
                        foreach($v as $key => $val){
                            if(is_int($key)){
                                $rules[$temp_group][$ruletype][$k][$val] = true;
                                unset($rules[$temp_group][$ruletype][$k][$key]);
                            }
                        }
                    }
                }
            }
        }
        $inherited_rules[] = $rules[$temp_group];
        if(!isset($rules[$temp_group]['parent'])){
            break;
        } else {
            $temp_group = $rules[$temp_group]['parent'];
        }
    }

    if(count($inherited_rules) == 1){
        $inherited_rules[1] = $inherited_rules[0];
        $inherited_rules[0] = array(
            'access' => array(),
            'deny' => array(),
        );
    }
    krsort($inherited_rules);
    $group_rules = $inherited_rules[count($inherited_rules) - 1];
    foreach ($inherited_rules as $k => $v) {
        if($k != 0){
            $next_rules = $inherited_rules[$k - 1];
            if(!isset($group_rules['deny'])){
                $group_rules['deny'] = true;
            }
            if(!isset($group_rules['access'])){
                $group_rules['access'] = true;
            }
            foreach(array('deny','access',) as $ruletype){
                $alttype = ($ruletype == 'deny') ? 'access' : 'deny';
                if(is_array($next_rules[$ruletype])){
                    if(is_array($group_rules[$ruletype])){
                        foreach($next_rules[$ruletype] as $nc => $na){
                        echo($nc);
                            if(isset($group_rules[$alttype][$nc])){
                                if(is_array($na)){
                                    if(is_array($group_rules[$alttype][$nc])){
                                        foreach ($na as $key => $val) {
                                            unset($group_rules[$alttype][$nc][$key]);
                                            unset($group_rules[$alttype][$nc][$val]);
                                        }
                                    } else {
                                    }
                                } else {
                                    unset($group_rules[$alttype][$nc]);
                                }
                            } else {
                                if(!is_array($group_rules[$alttype])){
                                    $group_rules[$alttype] = array();
                                }
                                $group_rules[$ruletype][$nc] = $na;
                            }
                        }
                    } else {
                        foreach ($next_rules[$ruletype] as $nc => $na) {
                            echo $nc.'->';
                            if(is_array($na)) {
                                foreach ($na as $kk => $vv) {
                                    echo $na;
                                    if(isset($group_rules[$alttype][$nc][$kk])){
                                        unset($group_rules[$alttype][$nc][$kk]);
                                    } else {
                                        if(!is_array($group_rules[$ruletype])){
                                            $group_rules[$ruletype] = array();
                                        }
                                        $group_rules[$ruletype][$nc][$kk] = true;
                                    }
                                }
                            } else {
                                $group_rules[$ruletype][$nc] = true;
                            }
                        }
                    }
                } elseif ($next_rules[$ruletype] === true) {
                    $group_rules[$ruletype] = true;
                }
            }
        }
    }
    die('<pre>'.print_r($group_rules, 1).print_r($inherited_rules, 1));
    $access = false;
    if(is_array($group_rules['deny'])){
        if(is_array($group_rules['access'])){
        // ---- // ---- //
            $access = true;
            if(
                    isset($group_rules['deny'][getResponse('controller')]) and
                    isset($group_rules['access'][getResponse('controller')])
                    ){
                if(is_array($group_rules['deny'][getResponse('controller')])){
                    if(isset($group_rules['deny'][getResponse('controller')][getResponse('action')])){
                        $access = false;
                    }
                } else {

                }
            } elseif(isset($group_rules['deny'][getResponse('controller')])){
                if(is_array($group_rules['deny'][getResponse('controller')])){
                    if(isset($group_rules['deny'][getResponse('controller')][getResponse('action')])){
                        $access = false;
                    }
                } else {
                    $access = false;
                }
            }

        // ---- // ---- //
        } else { // $group_rules['access'] == true
            if(!isset($group_rules['deny'][getResponse('controller')])){
                $access = true;
            } else {
                if(is_array($group_rules['deny'][getResponse('controller')])){
                    if(!isset($group_rules['deny'][getResponse('controller')][getResponse('action')])){
                        $access = true;
                    }
                } else {

                }
            }
        }
    } else { // $group_rules['deny'] == true
        if(is_array($group_rules['access'])){
            if(isset($group_rules['access'][getResponse('controller')])){
                if(is_array($group_rules['access'][getResponse('controller')])){
                    if(isset($group_rules['access'][getResponse('controller')][getResponse('action')])){
                        $access = true;
                    }
                } else {
                    $access = true;
                }
            }
        } else { // $group_rules['access'] == true

        }
    }
    function accessError(){
        redirect();
    }
    if(!$access){
        $acl['deny_handler']();
    }

}