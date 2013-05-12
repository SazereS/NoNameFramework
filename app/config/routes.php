<?php

return array(
    # Индексная страница
    ''                               => 'index/index',
    # Просто роут
    '{id|int}'                       => 'index/index/id/{id}',
    # Роуты для тестов
    'test/{test|3}'                  => 'index/index/result/success1/param/{test}',
    'test/{test|int}'                => 'index/index/result/success2/param/{test}',
    'test/{test|str|5}'              => 'index/index/result/success3/param/{test}',
    'test/{test|string}'             => 'index/index/result/success4/param/{test}',
    # Айди после контроллера запускает экшн 'view'
    '{controller}/{id|int}'          => '{controller}/view/id/{id}',
    # Все айдишники идут сразу после экшна
    '{controller}/{action}/{id|int}' => '{controller}/{action}/id/{id}',
);
