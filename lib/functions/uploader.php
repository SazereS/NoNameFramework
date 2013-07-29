<?php

function uploadFile($file, $path, $new_name = false, $max_size = false, Array $accepted_types = array()){
    if($file['error'] != 0){
        return false;
    }
    $name_arr = explode('.', $file['name']);
    if(count($name_arr) > 1){
        $ext = '.' . end($name_arr);
        unset($name_arr[count($name_arr) - 1]);
        $file['name'] = implode('.', $name_arr);
    } else {
        $ext = '';
    }
    $file['name'] = $new_name ? $new_name : $file['name'];
    if($max_size AND ($max_size < $file['size'])){
        return false;
    }
    if(!empty($accepted_types) AND !in_array($file['type'], $accepted_types)){
        return false;
    }
    $path = trim($path, ' /\\');
    $file['name'] = trim($file['name'], ' /\\');
    if(move_uploaded_file($file['tmp_name'], $path . DIRECTORY_SEPARATOR . $file['name'] . $ext)){
        $r['name'] = $file['name'] . $ext;
        $r['path'] = $path;
        $r['ext'] = $ext;
        return $r;
    } else {
        return false;
    }
}
