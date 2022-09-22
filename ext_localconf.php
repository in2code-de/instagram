<?php

if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

call_user_func(
    function () {
        /**
         * Include Frontend Plugins
         */
        \TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
            'In2code.instagram',
            'Pi1',
            [
                In2code\Instagram\Controller\ProfileController::class => 'show'
            ]
        );

        /**
         * Caching framework
         */
        if (!is_array(($GLOBALS['TYPO3_CONF_VARS']['SYS']['caching']['cacheConfigurations']['instagram'] ?? 0))) {
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
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPageTSConfig(
            '@import "EXT:instagram/Configuration/TSConfig/ContentElementWizard.typoscript"'
        );
    }
);
