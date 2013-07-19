<?php

global $_translates;
$_translates = array(
    'default_language' => 'en',
    'accepted_languages' => array(),
    'lang' => $_SESSION['lang']
);


function __($key){
    global $_translates;
    if($_translates['keys'][$key])
        return $_translates['keys'][$key];
    else
        return $key;
}

function translatorSetLanguage($lang){
    global $_translates;
    if(in_array($lang, $_translates['accepted_languages'])){
        $_translates['lang'] = $lang;
        $_SESSION['lang'] = $lang;
        return true;
    } else {
        $_translates['lang'] = $_translates['default_language'];
        $_SESSION['lang'] = $_translates['default_language'];
        return false;
    }
}

function translatorGetLanguage(){
    global $_translates;
    return $_translates['lang'];
}

function translatorUseDb($table = 'languages'){
    global $_translates;
    $_translates['source'] = 'db';
    $_translates['table'] = $table;
}

function translatorUseFiles($path = 'languages', $prefix = 'lang_'){
    global $_translates;
    $_translates['source'] = 'file';
    $_translates['path'] = $path;
    $_translates['prefix'] = $prefix;
}

function translatorSetAcceptedLanguages(Array $langs){
    global $_translates;
    $_translates['accepted_languages'] = $langs;
}

function translatorSetDefaultLanguages($lang){
    global $_translates;
    $_translates['default_language'] = $lang;
}

function translatorStart(){
    global $_translates;
    if(!translatorGetLanguage()){
        translatorSetLanguage($_translates['default_language']);
    }
    if($_translates['source'] == 'db'){
        $res = query('SELECT `key`,`' . $_translates['lang'] . '` FROM ' . $_translates['table']);
        while($row = fetch($res)){
            $_translates['keys'][$row['key']] = $row[$_translates['lang']];
        }
        return true;
    } elseif($_translates['source'] == 'file'){
        $_translates['keys'] = include(APP_PATH . $_translates['path'] . DIRECTORY_SEPARATOR . $_translates['prefix'] . $_translates['lang'] . '.php');
        return true;
    } else {
        return false;
    }
}

function translatorConfigure(Array $config){
    global $_translates;
    $_translates = array_merge($_translates, $config);
}