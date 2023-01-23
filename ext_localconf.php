<?php

use In2code\Instagram\Controller\ProfileController;
use TYPO3\CMS\Extbase\Utility\ExtensionUtility;
use TYPO3\CMS\Core\Utility\ExtensionManagementUtility;

if (!defined('TYPO3')) {
    die('Access denied.');
}

call_user_func(
    function () {
        /**
         * Include Frontend Plugins
         */
        ExtensionUtility::configurePlugin(
            'Instagram',
            'Pi1',
            [ProfileController::class => 'show'],
            [],
            ExtensionUtility::PLUGIN_TYPE_PLUGIN
        );

        /**
         * Caching framework
         */
        if (!isset($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['instagram'])) {
            $GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['instagram'] = [];
        }

        /**
         * UserFunc for TCA and FlexForm
         */
        $GLOBALS['TYPO3_CONF_VARS']['SYS']['formEngine']['nodeRegistry'][1615740358] = [
            'nodeName' => 'instagramGetToken',
            'priority' => 50,
            'class' => \In2code\Instagram\Tca\GetToken::class,
        ];

        /**
         * ContentElementWizard
         */
        ExtensionManagementUtility::addPageTSConfig(
            '@import "EXT:instagram/Configuration/TSConfig/ContentElementWizard.typoscript"'
        );
    }
);
