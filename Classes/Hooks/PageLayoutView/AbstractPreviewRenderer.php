<?php
declare(strict_types=1);
namespace In2code\Instagram\Hooks\PageLayoutView;

use In2code\Instagram\Exception\ConfigurationException;
use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Service\FlexFormService;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Object\Exception;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class AbstractPreviewRenderer
 */
abstract class AbstractPreviewRenderer implements PageLayoutViewDrawItemHookInterface
{
    /**
     * @var array tt_content.*
     */
    protected $data = [];

    /**
     * Define a CType
     *
     * @var string
     */
    protected $cType = '';

    /**
     * Define a list_type
     *
     * @var string
     */
    protected $listType = '';

    /**
     * @var string
     */
    protected $templatePath = 'EXT:instagram/Resources/Private/Templates/PreviewRenderer/';

    /**
     * AbstractPreviewRenderer constructor.
     * @throws ConfigurationException
     */
    public function __construct()
    {
        if ($this->cType === '') {
            throw new ConfigurationException('Property cType must not be empty', 1605299687);
        }
        if ($this->listType === '') {
            throw new ConfigurationException('Property listType must not be empty', 1605299725);
        }
    }

    /**
     * @param PageLayoutView $parentObject Calling parent object
     * @param bool $drawItem Whether to draw the item using the default functionality
     * @param string $headerContent Header content
     * @param string $itemContent Item content
     * @param array $row Record row of tt_content
     * @return void
     * @throws ConfigurationException
     * @throws Exception
     */
    public function preProcess(
        PageLayoutView &$parentObject,
        &$drawItem,
        &$headerContent,
        &$itemContent,
        array &$row
    ) {
        $this->data = &$row;
        if ($this->isMatching() && $this->checkTemplateFile()) {
            $drawItem = false;
            $headerContent = $this->getHeaderContent();
            $itemContent .= $this->getBodytext();
        }
    }

    /**
     * @return string
     */
    protected function getHeaderContent(): string
    {
        return '<div id="element-tt_content-' . (int)$this->data['uid']
            . '" class="t3-ctype-identifier " data-ctype="' . $this->cType . '"></div>';
    }

    /**
     * @return string
     * @throws Exception
     */
    protected function getBodytext(): string
    {
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $standaloneView->setTemplatePathAndFilename($this->getTemplateFile());
        $standaloneView->assignMultiple($this->getAssignmentsForTemplate() + [
            'data' => $this->data,
            'flexForm' => $this->getFlexForm()
        ]);
        return $standaloneView->render();
    }

    /**
     * Can be extended from children classes
     *
     * @return array
     */
    protected function getAssignmentsForTemplate(): array
    {
        return [];
    }

    /**
     * @return array
     */
    protected function getFlexForm(): array
    {
        $flexFormService = GeneralUtility::makeInstance(FlexFormService::class);
        return $flexFormService->convertFlexFormContentToArray($this->data['pi_flexform']);
    }

    /**
     * @return bool
     */
    protected function isMatching(): bool
    {
        return $this->data['CType'] === $this->cType && $this->data['list_type'] === $this->listType;
    }

    /**
     * @return bool
     * @throws ConfigurationException
     */
    protected function checkTemplateFile(): bool
    {
        if (is_file($this->getTemplateFile()) === false) {
            throw new ConfigurationException(
                'Expected template file for preview rendering for list_type ' . $this->listType . ' is missing',
                1605299974
            );
        }
        return true;
    }

    /**
     * Get absolute path to template file
     *
     * @return string
     */
    protected function getTemplateFile(): string
    {
        return GeneralUtility::getFileAbsFileName(
            $this->templatePath . GeneralUtility::underscoredToUpperCamelCase($this->data['list_type']) . '.html'
        );
    }
}
