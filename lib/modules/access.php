<?php

// Модуль ACL
$acl = getValue('acl');
if($acl['active'] === true){
    $rules = loadConfig('access');
    $temp_group = $group = (getResponse('group')) ? getResponse('group') : $acl['default_group'];
    while (true){
        $inherited_rules[] = $rules[$temp_group];
        if(!isset($rules[$temp_group]['parent'])){
            break;
        } else {
            $temp_group = $rules[$temp_group]['parent'];
        }
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
                        foreach($group_rules[$ruletype] as $nc => $na){
                            if(is_int($nc)){
                                $group_rules[$ruletype][$na] = true;
                                unset($group_rules[$ruletype][$nc]);
                            }
                        }
                        foreach($next_rules[$ruletype] as $nc => $na){
                            if(is_int($nc)){
                                $nc = $na;
                                $na = true;
                            } elseif(is_array($na)){
                                foreach ($na as $kk => $vv) {
                                    if (is_int($kk)) {
                                        $group_rules[$ruletype][$nc][$vv] = true;
                                        unset($group_rules[$ruletype][$nc][$kk]);
                                    }
                                }
                            }
                            if(isset($group_rules[$alttype][$nc])){
                                if(is_array($group_rules[$alttype][$nc])){
                                    foreach ($group_rules[$alttype][$nc] as $kk => $vv) {
                                        if (is_int($kk)) {
                                            $group_rules[$alttype][$nc][$vv] = true;
                                            unset($group_rules[$alttype][$nc][$kk]);
                                        }
                                    }
                                }
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
                            }
                        }
                    } else {
                        foreach ($next_rules[$ruletype] as $nc => $na) {
                            if(is_int($nc)){
                                if(isset($group_rules[$alttype][$na])){
                                    unset($group_rules[$alttype][$na]);
                                } else {
                                    if (!is_array($group_rules[$ruletype])) {
                                        $group_rules[$ruletype] = array();
                                    }
                                    $group_rules[$ruletype][$na] = true;
                                }
                            } elseif(is_array($na)) {
                                foreach ($na as $k => $v) {
                                    if(isset($group_rules[$alttype][$nc][$v])){
                                        unset($group_rules[$alttype][$nc][$v]);
                                    } else {
                                        if(!is_array($group_rules[$ruletype])){
                                            $group_rules[$ruletype] = array();
                                        }
                                        $group_rules[$ruletype][$nc][$v] = true;
                                    }
                                }
                            }
                        }
                    }
                } elseif ($next_rules[$ruletype] === true) {
                    $group_rules[$ruletype] = true;
                }
            }
        }
    }
    setParam('debug',$group_rules);
}