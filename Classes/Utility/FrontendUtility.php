<?php
declare(strict_types=1);
namespace In2code\Instagram\Utility;

use TYPO3\CMS\Frontend\Controller\TypoScriptFrontendController;

/**
 * Class FrontendUtility
 */
class FrontendUtility
{
    /**
     * @return int
     */
    public static function getCurrentPageIdentifier(): int
    {
        if (self::getTyposcriptFrontendController() !== null) {
            return (int)self::getTyposcriptFrontendController()->id;
        }
        return 0;
    }

    /**
     * @return TypoScriptFrontendController
     * @SuppressWarnings(PHPMD.Superglobals)
     */
    public static function getTyposcriptFrontendController(): ?TypoScriptFrontendController
    {
        return array_key_exists('TSFE', $GLOBALS) ? $GLOBALS['TSFE'] : null;
    }
}
