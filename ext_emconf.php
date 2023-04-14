<?php

$EM_CONF['news_tldr'] = [
    'title' => 'news_tldr',
    'description' => 'Creates a short summary for news via ChatGPT',
    'category' => 'plugin',
    'constraints' => [
        'depends' => [
            'typo3' => '12.3.0-12.9.99',
            'news' => '11.0.0-11.9.99',
        ],
        'conflicts' => [],
        'suggests' => [],
    ],
    'state' => 'stable',
    'clearCacheOnLoad' => true,
    'author' => 'Guido Schmechel',
    'author_email' => 'info@ayacoo.de',
    'author_company' => 'ayacoo',
    'version' => '1.0.0',
];
