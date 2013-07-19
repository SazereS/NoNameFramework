<?php

if(getParam('id')){
    texts_delete(getParam('id'));
}

redirect('texts');