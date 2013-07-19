<?php

$_view['text'] = texts_find(getParam('id'));

if(!$_view['text']){
    redirect('texts');
}

setResult($_view['text']);