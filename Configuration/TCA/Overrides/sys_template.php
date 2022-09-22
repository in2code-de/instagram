<?php
defined('TYPO3_MODE') || die();

/**
 * Register Static Template
 */

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'instagram',
    'Configuration/TypoScript',
    'Instagram'
);