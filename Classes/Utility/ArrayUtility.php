<?php
declare(strict_types=1);
namespace In2code\Instagram\Utility;

/**
 * Class ArrayUtility
 */
class ArrayUtility
{
    /**
     * @param \SimpleXMLElement $rss
     * @return array
     */
    public static function convertRssXmlToArray(\SimpleXMLElement $rss): array
    {
        return self::simpleXml2ArrayWithCdataSupport($rss);
    }

    /**
     * @param \SimpleXMLElement|array $xml
     * @return array|string
     */
    protected static function simpleXml2ArrayWithCdataSupport($xml)
    {
        $array = (array)$xml;
        if (count($array) === 0) {
            return (string)$xml;
        }
        foreach ($array as $key => $value) {
            if (is_iterable($value) === false) {
                continue;
            }
            $array[$key] = self::simpleXml2ArrayWithCdataSupport($value);
        }
        return $array;
    }
}
