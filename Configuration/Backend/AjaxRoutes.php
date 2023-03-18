<?php

use Ayacoo\NewsTldr\Controller\SummarizeController;

return [
    // Save a newly added online media
    'ayacoo_news_tldr_summarize' => [
        'path' => '/ayacoo-news-tldr/summarize',
        'target' => SummarizeController::class . '::updateTeaserAction',
    ],
];