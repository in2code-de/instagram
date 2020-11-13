<?php
defined('TYPO3_MODE') || die();

call_user_func(
    function () {

        /**
         * Register own preview renderer for plugins
         */
        $layout = 'cms/layout/class.tx_cms_layout.php';
        $GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS'][$layout]['tt_content_drawItem']['instagram_pi1'] =
        \In2code\Instagram\Hooks\PageLayoutView\Pi1PreviewRenderer::class;
    }
);
