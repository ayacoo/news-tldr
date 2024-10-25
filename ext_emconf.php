<?php

$EM_CONF['news_tldr'] = [
    'title' => 'news_tldr',
    'description' => 'Creates a short summary for news via ChatGPT',
    'category' => 'plugin',
    'constraints' => [
        'depends' => [
            'typo3' => '13.0.0-13.4.99',
            'news' => '12.0.0-12.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'author' => 'Guido Schmechel',
    'author_email' => 'info@ayacoo.de',
    'author_company' => 'ayacoo',
    'version' => '2.0.0',
];
