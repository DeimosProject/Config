<?php

include_once dirname(__DIR__) . '/vendor/autoload.php';

$builder = new \Deimos\Builder\Builder();
$helper  = new \Deimos\Helper\Helper($builder);

// php
$config = new \Deimos\Config\Config($helper, __DIR__ . '/php', [
    'withParameters' => false
]);

$slice = $config->get('directories');

$slice['dir2'] = [
    'images' => [
        'my'         => ['image.jpg'],
        'collection' => [
            '2017' => [
                'praga' => '%collection.2017.praga%'
            ]
        ]
    ]
];

$config->saveFile('directories2', $slice);

// json
$config = new \Deimos\Config\Config(
    $helper,
    __DIR__ . '/json',
    [
        'extension' => \Deimos\Config\JSON,
        'withParameters' => false
    ]
);

$slice = $config->get('directories');

$slice['dir2'] = [
    'images' => [
        'my'         => ['image.jpg'],
        'collection' => [
            '2017' => [
                'praga' => '%collection.2017.praga%'
            ]
        ]
    ]
];

$config->saveFile('directories2', $slice);

// yml
$config = new \Deimos\Config\Config(
    $helper,
    __DIR__ . '/yml',
    [
        'extension'      => \Deimos\Config\YML,
        'withParameters' => false
    ]
);

$slice = $config->get('directories');

$slice['dir2'] = [
    'images' => [
        'my'         => ['image.jpg'],
        'collection' => [
            '2017' => [
                'praga' => '%collection.2017.praga%'
            ]
        ]
    ]
];

$config->saveFile('directories2', $slice);
