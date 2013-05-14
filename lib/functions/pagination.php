<?php

function pagination ($elements_count, $limit){
    $pages = ($elements_count - ($elements_count % $limit)) / $limit;
	return (($elements_count % $limit) == 0) ? $pages : $pages + 1;
}
