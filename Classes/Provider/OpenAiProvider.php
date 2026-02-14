<?php

namespace Ayacoo\NewsTldr\Provider;

use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class OpenAiProvider implements AiProviderInterface
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
        $token = trim($extConf['token'] ?? '');

        if (empty($token)) {
            throw new \RuntimeException(LocalizationUtility::translate('no_valid_token', self::EXTENSION_KEY));
        }

        $payload = [];
        $payload['model'] = $extConf['model'] ?? 'gpt-3.5-turbo';
        $messages = new \stdClass();
        $messages->role = 'user';
        $messages->content = $content;
        $payload['messages'] = [$messages];

        $additionalOptions = [
            'body' => json_encode($payload),
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Cache-Control' => 'no-cache',
                'Content-Type' => 'application/json'
            ],
        ];
        $response = $this->requestFactory->request(
            'https://api.openai.com/v1/chat/completions',
            'POST',
            $additionalOptions
        );

        if ($response->getStatusCode() !== 200) {
            throw new \RuntimeException(
                LocalizationUtility::translate('status_code_is', self::EXTENSION_KEY) . $response->getStatusCode()
            );
        }
        if ($response->getHeaderLine('Content-Type') !== 'application/json') {
            throw new \RuntimeException(
                LocalizationUtility::translate('no_valid_json', self::EXTENSION_KEY)
            );
        }

        $responseContent = $response->getBody()->getContents();
        try {
            $result = json_decode($responseContent, true, flags: JSON_THROW_ON_ERROR);
            return (string)($result['choices'][0]['message']['content'] ?? '');
        } catch (\JsonException) {
            throw new \RuntimeException(LocalizationUtility::translate('no_valid_json', self::EXTENSION_KEY));
        }
    }
}
