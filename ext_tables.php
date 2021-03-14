<?php
defined('TYPO3_MODE') || die();

call_user_func(
    function () {

        /**
         * Register icons
         */
        $iconRegistry =
            \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Imaging\IconRegistry::class);
        $iconRegistry->registerIcon(
            'extension-instagram',
            \TYPO3\CMS\Core\Imaging\IconProvider\SvgIconProvider::class,
            ['source' => 'EXT:instagram/Resources/Public/Icons/Extension.svg']
        );

        /**
         * Register own preview renderer for plugins
         */
        $layout = 'cms/layout/class.tx_cms_layout.php';
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$layout]['tt_content_drawItem']['instagram_pi1'] =
        \In2code\Instagram\Hooks\PageLayoutView\Pi1PreviewRenderer::class;
    }
);
