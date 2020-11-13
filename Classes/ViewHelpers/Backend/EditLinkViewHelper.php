<?php
declare(strict_types=1);
namespace In2code\Instagram\ViewHelpers\Backend;

use In2code\Instagram\Utility\BackendUtility;
use TYPO3\CMS\Backend\Routing\Exception\RouteNotFoundException;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class EditLinkViewHelper
 * @noinspection PhpUnused
 */
class EditLinkViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('identifier', 'int', 'Identifier', true);
        $this->registerArgument('table', 'string', 'Tablename', false, 'tt_content');
    }

    /**
     * @return string
     * @throws RouteNotFoundException
     */
    public function render(): string
    {
        $string = '<a href="';
        $string .= BackendUtility::createEditUri($this->arguments['table'], (int)$this->arguments['identifier']);
        $string .= '" class="in2template_editlink">';
        $string .= $this->renderChildren();
        $string .= '</a>';
        return $string;
    }
}
