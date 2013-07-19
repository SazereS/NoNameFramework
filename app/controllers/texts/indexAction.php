<?php

loadFunction('cropArticle');

$_view['texts'] = texts_getAll();

setResult($_view['texts']);