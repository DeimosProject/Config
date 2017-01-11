<?php

// $config->get('db') -- ConfigObject
// $config->get('db')->get() -- array
return [
    // $config->get('db')->get('dsn')
    'dsn' => 'conn',
    'login' => 'root',
    'password' => '',

    // $config->get('db:options.hello')
    'options' => [
        'hello' => 'world'
    ]
];
