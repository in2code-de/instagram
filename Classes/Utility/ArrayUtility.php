<?php
declare(strict_types=1);
namespace In2code\Instagram\Utility;

/**
 * Class ArrayUtility
 */
class ArrayUtility
{
    /**
     * @param string $string
     * @return bool
     */
    public static function isJsonArray(string $string): bool
    {
        if (empty($string)) {
            return false;
        }
        return is_array(json_decode($string, true));
    }
}
