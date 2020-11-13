<?php
declare(strict_types=1);
namespace In2code\Instagram\ViewHelpers\Backend;

use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class CatchViewHelper
 * @noinspection PhpUnused
 */
class CatchViewHelper extends AbstractViewHelper
{
    /**
     * @var bool
     */
    protected $escapeOutput = false;

    /**
     * @return string
     */
    public function render(): string
    {
        try {
            return $this->renderChildren();
        } catch (\Exception $exception) {
            $string = '<div class="alert alert-danger" role="alert">';
            $string .= $exception->getMessage();
            $string .= ' (' . $exception->getCode() . ')';
            $string .= '</div>';
            return $string;
        }
    }
}
