<?php

defined('TYPO3') or die();

$tempColumns = [
    'chat_gpt_summary' => [
        'label' => 'LLL:EXT:news_tldr/Resources/Private/Language/locallang_db.xlf:tx_news_domain_model_news.chat_gpt_summary',
        'config' => [
            'type' => 'user',
            'renderType' => 'specialField',
        ],
    ],
];

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns(
    'tx_news_domain_model_news',
    $tempColumns
);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes(
    'tx_news_domain_model_news',
    'chat_gpt_summary',
    '',
    'after:teaser'
);
