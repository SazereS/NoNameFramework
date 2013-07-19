<?php

return array(
    # Индексная страница
    ''                               => 'index/index',
    # Стандартные маршруты
    '{controller}/{id|int}' => '{controller}s/view/id/{id}',
    '{controller}/{id|int}/{action}' => '{controller}s/{action}/id/{id}',
);
