<?php

function texts_getAll(){
    return fetchAll('texts');
}

function texts_find($id){
    return findRow('texts', $id);
}

function texts_edit($id, $post){
    return updateRow('texts', 'id = ' . intval($id), $post);
}

function texts_insert($post){
    return insertRow('texts', $post);
}

function texts_delete($id){
    return deleteRow('texts', 'id = ' . intval($id));
}