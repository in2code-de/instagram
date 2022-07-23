<?php
$EM_CONF[$_EXTKEY] = [
    'title' => 'instagram',
    'description' => 'Show images and posts from instagram profiles',
    'category' => 'plugin',
    'version' => '7.0.0',
    'author' => 'Alex Kellner',
    'author_email' => 'alexander.kellner@in2code.de',
    'author_company' => 'in2code.de',
    'state' => 'stable',
    'constraints' => [
        'depends' => [
            'typo3' => '10.4.0-11.5.99',
            'php' => '7.2.0-8.0.99'
        ],
        'conflicts' => [],
        'suggests' => [],
    ]
];
