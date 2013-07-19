<?php

function addActiveClass ($input){
    global $_url;
    $url = explode('.', trim($_url, '/'));
    $url = $url[0];
    if(is_array($input)){
        if(in_array($url, $input)){
            echo ' active';
        }
    } elseif($input == $url){
        echo ' active';
    }
}
