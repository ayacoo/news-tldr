<?php

namespace Ayacoo\NewsTldr\Provider;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class ProviderFactory
{
    private const EXTENSION_KEY = 'news_tldr';

    public function __construct(
        private readonly ExtensionConfiguration $extensionConfiguration
    ) {
    }

    public function getProvider(): AiProviderInterface
    {
        $extConf = $this->extensionConfiguration->get(self::EXTENSION_KEY);
        $providerType = $extConf['provider'] ?? 'openai';

        return match ($providerType) {
            'gemini' => GeneralUtility::makeInstance(GeminiProvider::class),
            default => GeneralUtility::makeInstance(OpenAiProvider::class),
        };
    }
}
