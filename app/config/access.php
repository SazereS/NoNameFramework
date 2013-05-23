<?php

return array(
    'test' => array(
        'access' => array(
            'forAll',
            'index' => array(
                'index',
                'register'
            )
        ),
        'deny' => true
    ),
    'guest' => array(
        'access' => true,
        'deny' => array(
            'index' => array(
                'phpInfo'
            ),
            'forUsers',
            'forAdmins'
        ),
    ),
    'user' => array(
        'parent' => 'guest',
        'access' => array(
            'forUsers',
            'index' => array(
                'phpInfo'
            ),
            'forAdmins' => array(
                'test'
            ),
            'notForAll'
        ),
        'deny' => array(
            'index' => array(
                'register'
            )
        )
    ),
    'admin' => array(
        'access' => true
    )
);
/*
 *
 * 'guest' => array(
        'access' => array(
            'forAdmins' => array(
                'test'
            ),
            'notForAll'
        ),
        'deny' => array(
            'index' => array(
                'register'
            ),
            'forAdmins'
        ),
    ),
 *
 */