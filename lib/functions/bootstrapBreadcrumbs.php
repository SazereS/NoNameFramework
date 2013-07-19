<?php

function bootstrapBreadcrumbs(Array $links){
    $i = count($links);
    foreach($links as $k=>$v){
        $i--;
        if($i > 0){
            $result[] = '<li><a href="' . baseUrl($k) . '">' . $v . '</a> ';
        } else {
            $result[] = '<li class="active">' . $v;
        }
    }
    return '<ul class="breadcrumb">' . "\n" . implode($result, '<span class="divider">/</span></li>' . "\n") . '</li>' . "\n" . '</ul>';
}