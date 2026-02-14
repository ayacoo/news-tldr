<?php

namespace Ayacoo\NewsTldr\Controller;

use Ayacoo\NewsTldr\Event\ModifyChatGptContentEvent;
use Ayacoo\NewsTldr\Provider\ProviderFactory;
use Psr\EventDispatcher\EventDispatcherInterface;
use TYPO3\CMS\Backend\Utility\BackendUtility;
use TYPO3\CMS\Core\Configuration\ExtensionConfiguration;
use TYPO3\CMS\Core\Http\JsonResponse;

class SummarizeController
{
    private const EXTENSION_KEY = 'news_tldr';

    public function __construct(
        private readonly ExtensionConfiguration   $extensionConfiguration,
        private readonly EventDispatcherInterface $eventDispatcher,
        private readonly ProviderFactory          $providerFactory
    )
    {
    }

    public function updateTeaserAction(): JsonResponse
    {
        $extConf = $this->extensionConfiguration->get(self::EXTENSION_KEY);
        $summaryLength = (int)($extConf['length'] ?? 200);

        $request = $GLOBALS['TYPO3_REQUEST'];
        $postParams = $request->getParsedBody();

        $row = BackendUtility::getRecord('tx_news_domain_model_news', (int)$postParams['uid']);

        $content = 'Fasse mir diesen Text in ' . $summaryLength . ' Zeichen zusammen: ';
        $content .= strip_tags(trim($row['bodytext'] ?? ''));
        $modifyContentEvent = $this->eventDispatcher->dispatch(
            new ModifyChatGptContentEvent($row, $content)
        );

        try {
            $provider = $this->providerFactory->getProvider();
            $result = $provider->summarize($modifyContentEvent->getContent());
            return new JsonResponse([
                'text' => $result,
                'success' => true
            ]);
        } catch (\Exception $e) {
            return new JsonResponse([
                'text' => $e->getMessage(),
                'success' => false
            ]);
        }
    }
}
