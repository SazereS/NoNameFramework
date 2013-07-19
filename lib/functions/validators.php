<?php

// Data Validators

function validatorsGetRegEx($key = FALSE){
    $reg_ex = array(
        'int' => '/^[0-9]+$/',
        'float' => '/^[0-9]+(,|\.)?[0-9]*$/',
        'phone' => '/\+?[0-9]{0,4} ?\(?([0-9]{3})\)?([ .-]?)([- 0-9]{2,15})/',
        'login' => '/^[-_a-zA-Z0-9]+$/',
        'alpha' => '/^[a-zA-Z0-9]+$/',
        'url' => '|^https?://([\w-]+\.)+[\w]{2,4}(:[0-9]{1,5})?(/[\w-._%]+)*(\?.*)?(#[\w-.]*)?/?$|iu',
        'email' => '|^[\w-.]+@([\w-]+\.)+[\w]{2,4}$|iu',
        'ipv4' => '/^(\d{1,2}|1[0-9]{2}|2[0-4]{1}[0-9]{1}|25[0-5]{1}){1}(\.(\d{1,2}|1[0-9]{2}|2[0-4]{1}[0-9]{1}|25[0-5]{1})){3}$/',
        'slug' => '/^[\w ]+$/u'
    );

    if($key){
        return $reg_ex[$key];
    }
    return $reg_ex;
}

function validateRegEx($data, $regex){
    if(preg_match($regex, $data)){
        return true;
    }
    return false;
}

function validateInt($data){
    return validateRegEx($data, validatorsGetRegEx('int'));
}
function validateFloat($data){
    return validateRegEx($data, validatorsGetRegEx('float'));
}
function validatePhone($data){
    return validateRegEx($data, validatorsGetRegEx('phone'));
}
function validateLogin($data){
    return validateRegEx($data, validatorsGetRegEx('login'));
}
function validateAlpha($data){
    return validateRegEx($data, validatorsGetRegEx('alpha'));
}
function validateUrl($data){
    return validateRegEx($data, validatorsGetRegEx('url'));
}
function validateEmail($data){
    return validateRegEx($data, validatorsGetRegEx('email'));
}
function validateIpv4($data){
    return validateRegEx($data, validatorsGetRegEx('ipv4'));
}
function validateSlug($data){
    return validateRegEx($data, validatorsGetRegEx('slug'));
}
function validateNotEmpty($data){
    return !empty($data);
}
function validateInArray($data, Array $haystack){
    if(in_array($data, $haystack)){
        return true;
    }
    return false;
}
function validateLength($data, Array $rules){
    if(isset($rules['max']) AND (strlen($data) > $rules['max'])){
        return false;
    }
    if(isset($rules['min']) AND (strlen($data) < $rules['min'])){
        return false;
    }
    return true;
}
function validateRecordExists($data, $table, $field){
    $q = 'SELECT * FROM `' . $table . '` WHERE ' . $field . ' = \'' . addcslashes($data, '\'\\') . '\'';
    $result = num(query($q));
    if($result > 0){
        return true;
    }
    return false;
}
function validateRecordNotExists($data, $table, $field){
    $q = 'SELECT * FROM `' . $table . '` WHERE ' . $field . ' = \'' . addcslashes($data, '\'\\') . '\'';
    $result = num(query($q));
    if($result == 0){
        return true;
    }
    return false;
}
function validateIdentical($data, $origin){
    if($data === $origin){
        return true;
    }
    return false;
}

function validate(Array $data, Array $validate, &$errors = 'NOT_NEED'){
    if(function_exists('__')){
        $__ = '__';
    } else {
        $__ = function($key){
            return $key;
        };
    }
    $flag = true;
    foreach($validate as $validator => $fields){
        $nv[strtolower($validator)] = $fields;
        if(strtolower($validator) == 'notempty'){
            $not_empty = $fields;
        }
    }
    $validate = $nv;
    if(empty($not_empty)) $not_empty = array();
    foreach($validate as $validator => $fields){
        $validator = 'validate' . $validator;
        if(strtolower($validator) == 'validateidentical'){
            foreach($fields as $identical_fields){
                $origin_field = reset($identical_fields);
                foreach($identical_fields as $field){
                    if(!validateIdentical($data[$field], $data[$origin_field])){
                        $flag = false;
                        if($errors !== 'NOT_NEED'){
                            $errors[$field][] = $__(strtolower($validator));
                        } else {
                            return false;
                        }
                    }
                }
            }
        } elseif(validateInArray(strtolower($validator), array('validaterecordexists', 'validaterecordnotexists'))){
            foreach($fields as $field => $params){
                if(!validateNotEmpty($data[$field]) AND !validateInArray($field, $not_empty)){
                    continue;
                }
                if(!$validator($data[$field], $params['table'], $params['field'])){
                    $flag = false;
                    if($errors !== 'NOT_NEED'){
                        $errors[$field][] = $__(strtolower($validator));
                    } else {
                        return false;
                    }
                }
            }
        } elseif(strtolower($validator) == 'validateinarray'){
            $origin_fields = reset($fields);
            $haystack = next($fields);
            if($haystack === false){ debugTrace(); die('VALIDATOR ERROR! Haystack needed!'); }
            foreach($origin_fields as $field){
                if(!validateNotEmpty($data[$field]) AND !validateInArray($field, $not_empty)){
                    continue;
                }
                if(!validateInArray($data[$field], $haystack)){
                    $flag = false;
                    if($errors !== 'NOT_NEED'){
                        $errors[$field][] = $__(strtolower($validator));
                    } else {
                        return false;
                    }
                }
            }
        } elseif(strtolower($validator) == 'validatelength'){
            foreach($fields as $validatelength){
                $vf = reset($validatelength);
                $params = next($validatelength);
                foreach($vf as $field){
                    if(!validateNotEmpty($data[$field]) AND !validateInArray($field, $not_empty)){
                        continue;
                    }
                    if(!validateLength($data[$field], $params)){
                        $flag = false;
                        if($errors !== 'NOT_NEED'){
                            $errors[$field][] = $__(strtolower($validator));
                        } else {
                            return false;
                        }
                    }
                }
            }
        } else {
            foreach($fields as $field){
                if(!validateNotEmpty($data[$field]) AND !validateInArray($field, $not_empty)){
                    continue;
                }
                if(!$validator($data[$field])){
                    $flag = false;
                    if($errors !== 'NOT_NEED'){
                        $errors[$field][] = $__(strtolower($validator));
                    } else {
                        return false;
                    }
                }
            }
        }
    }
    return $flag;
}