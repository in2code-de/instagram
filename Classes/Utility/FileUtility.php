<?php
declare(strict_types=1);
namespace In2code\Instagram\Utility;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FileUtility
 */
class FileUtility
{
    /**
     * @param string $path absolute path
     * @return void
     */
    public static function createFolderIfNotExists(string $path)
    {
        if (!is_dir($path)) {
            try {
                GeneralUtility::mkdir_deep($path);
            } catch (\Exception $exception) {
                throw new \UnexpectedValueException('Folder ' . $path . ' could not be created', 1549533300);
            }
        }
    }
}
