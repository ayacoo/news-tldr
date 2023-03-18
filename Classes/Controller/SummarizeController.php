<?php

namespace Ayacoo\NewsTldr\Controller;

use Ayacoo\NewsTldr\Event\ModifyChatGptContentEvent;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Core\Http\RequestFactory;

class SummarizeController
{
    public function __construct(
        private readonly RequestFactory         $requestFactory,
        private readonly ExtensionConfiguration $extensionConfiguration,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
    }

    public function updateTeaserAction(): JsonResponse
    {
        $extConf = $this->extensionConfiguration->get('news_tldr');

        $request = $GLOBALS['TYPO3_REQUEST'];
        $postParams = $request->getParsedBody();

        $row = BackendUtility::getRecord('tx_news_domain_model_news', (int)$postParams['uid']);
        $content = 'Fasse mir diesen Text in 200 Zeichen zusammen: ' . strip_tags(trim($row['bodytext'] ?? ''));
        $modifyChatGptContentEvent = $this->eventDispatcher->dispatch(
            new ModifyChatGptContentEvent($row, $content)
        );

        $payload = [];
        $payload['model'] = $extConf['model'] ?? '';
        $messages = new \stdClass();
        $messages->role = 'user';
        $messages->content = $modifyChatGptContentEvent->getContent();
        $payload['messages'] = [$messages];

        $additionalOptions = [
            'body' => json_encode($payload),
            'headers' => [
                'Authorization' => 'Bearer ' . ($extConf['token'] ?? ''),
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
                'Returned status code is ' . $response->getStatusCode()
            );
        }
        if ($response->getHeaderLine('Content-Type') !== 'application/json') {
            throw new \RuntimeException(
                'The request did not return JSON data'
            );
        }
        $content = $response->getBody()->getContents();
        try {
            $result = json_decode($content, true, flags: JSON_THROW_ON_ERROR);
            return new JsonResponse(['text' => $result['choices'][0]['message']['content'] ?? '']);
        } catch (\JsonException) {
            return new JsonResponse(['text' => '']);
        }
    }
}
