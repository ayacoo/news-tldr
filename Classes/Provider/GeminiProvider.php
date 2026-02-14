<?php

namespace Ayacoo\NewsTldr\Provider;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class GeminiProvider implements AiProviderInterface
{
    private const EXTENSION_KEY = 'news_tldr';

    public function __construct(
        private readonly RequestFactory         $requestFactory,
        private readonly ExtensionConfiguration $extensionConfiguration
    ) {
    }

    public function summarize(string $content): string
    {
        $extConf = $this->extensionConfiguration->get(self::EXTENSION_KEY);
        $apiKey = trim($extConf['geminiApiKey'] ?? '');
        $model = trim($extConf['geminiModel'] ?? 'gemini-2.5-flash');

        if (empty($apiKey)) {
            throw new \RuntimeException(LocalizationUtility::translate('no_valid_token', self::EXTENSION_KEY));
        }

        $payload = [
            'contents' => [
                [
                    'parts' => [
                        ['text' => $content]
                    ]
                ]
            ]
        ];

        $url = sprintf(
            'https://generativelanguage.googleapis.com/v1beta/models/%s:generateContent?key=%s',
            $model,
            $apiKey
        );

        $additionalOptions = [
            'body' => json_encode($payload),
            'headers' => [
                'Content-Type' => 'application/json'
            ],
        ];

        $response = $this->requestFactory->request($url, 'POST', $additionalOptions);

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException(
                LocalizationUtility::translate('status_code_is', self::EXTENSION_KEY) . $response->getStatusCode()
            );
        }

        $responseContent = $response->getBody()->getContents();
        try {
            $result = json_decode($responseContent, true, flags: JSON_THROW_ON_ERROR);
            return (string)($result['candidates'][0]['content']['parts'][0]['text'] ?? '');
        } catch (\JsonException) {
            throw new \RuntimeException(LocalizationUtility::translate('no_valid_json', self::EXTENSION_KEY));
        }
    }
}
