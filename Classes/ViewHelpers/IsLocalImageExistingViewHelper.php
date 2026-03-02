<?php
declare(strict_types=1);
namespace In2code\Instagram\ViewHelpers;

use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\Rendering\RenderingContextInterface;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractConditionViewHelper;

/**
 * Class IsLocalImageExistingViewHelper
 * @noinspection PhpUnused
 */
class IsLocalImageExistingViewHelper extends AbstractConditionViewHelper
{
    /**
     * @var string
     */
    protected static $imageFolder = 'typo3temp/assets/tx_instagram/';

    /**
     * Initializes the arguments
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('id', 'int', 'post identifier', true);
    }

    /**
     * @param array $arguments
     * @param RenderingContextInterface $renderingContext
     * @return bool
     */
    public static function verdict(array $arguments, RenderingContextInterface $renderingContext): bool
    {
        $file = GeneralUtility::getFileAbsFileName(self::$imageFolder) . $arguments['id'] . '.jpg';
        return is_file($file);
    }
}
