<?php

// $config->get('db') -- ConfigObject
// $config->get('db')->get() -- array
return [
    // $config->get('db')->get('dsn')
    'dsn'      => '%db.dsn%',
    'login'    => '%db.login%',
    'password' => '%db.password%',

    // $config->get('db:options.hello')
    'options'  => '%db.options%'
];
