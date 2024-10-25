<?php

namespace Ayacoo\NewsTldr\Controller;

use Ayacoo\NewsTldr\Event\ModifyChatGptContentEvent;
use OpenAI;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\JsonResponse;
use TYPO3\CMS\Extbase\Utility\LocalizationUtility;

class SummarizeController
{
    private const EXTENSION_KEY = 'news_tldr';

    public function __construct(
        private readonly ExtensionConfiguration   $extensionConfiguration,
        private readonly EventDispatcherInterface $eventDispatcher
    )
    {
    }

    public function updateTeaserAction(): JsonResponse
    {
        $extConf = $this->extensionConfiguration->get(self::EXTENSION_KEY);
        $token = trim($extConf['token'] ?? '');
        $summaryLength = (int)($extConf['length'] ?? 200);

        if (empty($token)) {
            return new JsonResponse([
                'text' => LocalizationUtility::translate('no_valid_token', self::EXTENSION_KEY),
                'success' => false
            ]);
        }

        $request = $GLOBALS['TYPO3_REQUEST'];
        $postParams = $request->getParsedBody();

        $row = BackendUtility::getRecord('tx_news_domain_model_news', (int)$postParams['uid']);

        $content = 'Fasse mir diesen Text in ' . $summaryLength . ' Zeichen zusammen: ';
        $content .= strip_tags(trim($row['bodytext'] ?? ''));
        $modifyChatGptContentEvent = $this->eventDispatcher->dispatch(
            new ModifyChatGptContentEvent($row, $content)
        );

        $client = OpenAI::client($token);
        $response = $client->chat()->create([
            'model' => $extConf['model'],
            'messages' => [
                [
                    'role' => 'user',
                    'content' => $modifyChatGptContentEvent->getContent()
                ],
            ],
        ]);

        $result = $response->toArray();
        return new JsonResponse([
            'text' => (string)$result['choices'][0]['message']['content'] ?? '',
            'success' => true
        ]);
    }
}
