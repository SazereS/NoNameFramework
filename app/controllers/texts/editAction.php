<?php

$_view['text'] = texts_find(getParam('id'));

if(!$_view['text']){
    redirect('texts');
}

if(isPost()){
    $post = getPost();
    loadFunction('validators');
    if(
        validate(
            $post,
            array(
                'NotEmpty' => array('text'),
                'Length' => array(
                    array(
                        array('text'),
                        array('min' => 10)
                    )
                )
            )
        )
    ){
        texts_edit(getParam('id'), $post);
        redirect('text/' . getParam('id'));
    }
}
$_view['action'] = getResponse('action');