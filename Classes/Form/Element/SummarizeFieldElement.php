<?php
declare(strict_types=1);

namespace Ayacoo\NewsTldr\Form\Element;

use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Page\PageRenderer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

class SummarizeFieldElement extends AbstractFormElement
{
    /**
     * @see https://docs.typo3.org/m/typo3/reference-tca/main/en-us/ColumnsConfig/Type/User/Index.html
     * @return array
     */
    public function render(): array
    {
        $pageRenderer = GeneralUtility::makeInstance(PageRenderer::class);
        $pageRenderer->loadJavaScriptModule('@news_tldr/summarize.js');
        $pageRenderer->addInlineLanguageLabelFile('EXT:news_tldr/Resources/Private/Language/locallang.xlf');

        $row = $this->data['databaseRow'];

        $fieldInformationResult = $this->renderFieldInformation();
        $resultArray = $this->mergeChildReturnIntoExistingResult($this->initializeResultArray(), $fieldInformationResult, false);

        $html = [];
        $html[] = '<div class="formengine-field-item t3js-formengine-field-item">';
        $html[] = '<div class="form-wizards-wrap">';
        $html[] = '<div class="form-wizards-element">';
        $html[] = '<div class="form-control-wrap">';


        $html[] = '<button class="btn btn-default t3js-ayacoo-tldr" data-newsid="' . $row['uid'] . '">
         <span class="t3js-icon icon icon-size-small icon-state-default icon-apps-pagetree-page-default" data-identifier="apps-pagetree-page-default">
             <span class="icon-markup">
                <svg class="icon-color"><use xlink:href="/typo3/sysext/core/Resources/Public/Icons/T3Icons/sprites/apps.svg#apps-pagetree-page-default"></use></svg>
             </span>
         </span>
        Start!
        </button>';

        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';
        $html[] = '</div>';

        $resultArray['html'] = implode(LF, $html);

        return $resultArray;
    }
}
