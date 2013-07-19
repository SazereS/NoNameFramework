<?php

function cropArticle($text, $max_words, $append = '…') {
    $words = explode(' ', $text, $max_words + 1);
    if(count($words) <= $max_words){
        return $text;
    }
    array_pop($words);
    $text = implode(' ', $words) . $append;
    return $text;
}