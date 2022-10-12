<?php
defined('TYPO3') || die();

/**
 * Register Static Template
 */

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile(
    'instagram',
    'Configuration/TypoScript',
    'Instagram'
);