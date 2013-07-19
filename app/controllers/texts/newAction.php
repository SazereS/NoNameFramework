<?php

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
        redirect('text/' . texts_insert($post));
    }
    $_view['text'] = $post;
}
$_view['action'] = getResponse('action');
setResponse('action', 'edit');