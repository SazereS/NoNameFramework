<?php

function distribution ($addresses, $text, $values = array(), $col_key = false){
    $errors = 0;
    foreach ($values as $key => $value) {
        $values['{'.$key.'}'] = iconv('WINDOWS-1251','UTF-8', $value);
        unset($values[$key]);
    }
    $headers  = "Content-type: text/html; charset=utf-8 \r\n";
    $headers .= "From: Noreply <noreply@".str_replace('www.', '', $values['{site}']).">\r\n";
    if($key){
        foreach($addresses as $v){
            foreach ($v as $key => $value) {
                $v['{'.$key.'}'] = $value;
                unset($v[$key]);
            }
            if(!mail($v['{'.$col_key.'}'], $values['{title}'], strtr($text, array_merge($v,$values)), $headers)){
                $errors++;
            }
        }
    } else {
        foreach($addresses as $v){
            if(!mail($v, $values['{title}'], strtr($text, $values), $headers)){
                $errors++;
            }
        }
    }
    return $errors;
}