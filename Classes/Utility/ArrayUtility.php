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

    /**
     * [
     *  'settings.username' => ['vDEF' => 'in2code.de']],
     *  'settings.appId' => ['vDEF' => '123abc']],
     * ]
     *
     * =>
     *
     * [
     *  'username' => 'in2code.de',
     *  'appId' => '123abc'
     * ]
     *
     * @param array $flexForm
     * @return array
     */
    public static function cleanFlexFormArray(array $flexForm): array
    {
        $result = [];
        foreach ($flexForm as $key => $value) {
            preg_match('/settings.(.*)/', $key, $keyResult);
            if (!empty($keyResult[1]) && array_key_exists('vDEF', $value)) {
                $result[$keyResult[1]] = $value['vDEF'];
            }
        }
        return $result;
    }
}
