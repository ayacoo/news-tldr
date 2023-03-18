<?php

return [
    'dependencies' => [
        'core',
        'backend'
    ],
    'tags' => [
        'ayacoo.news_tldr',
    ],
    'imports' => [
        '@news_tldr/' => 'EXT:news_tldr/Resources/Public/JavaScript/',
    ],
];
