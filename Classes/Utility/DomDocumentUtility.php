<?php
declare(strict_types=1);
namespace In2code\Instagram\Utility;

/**
 * Class DomDocumentUtility
 */
class DomDocumentUtility
{
    /**
     * @param string $html
     * @return string
     */
    public static function getFirstImageSourceFromString(string $html): string
    {
        $source = '';
        $dom = new \DOMDocument();
        try {
            @$dom->loadHTML(
                self::wrapHtmlWithMainTags($html),
                LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD
            );
            $tags = $dom->getElementsByTagName('img');
            /** @var \DOMElement $tag */
            foreach ($tags as $tag) {
                if ($tag->hasAttribute('src')) {
                    $source = $tag->getAttribute('src');
                    break;
                }
            }
        } catch (\Exception $exception) {
            throw new \UnexpectedValueException('Description could not be parsed', 1591349375);
        }
        return $source;
    }

    /**
     * Wrap html with "<?xml encoding="utf-8" ?><html><body>|</body></html>"
     *
     *  This is a workarround for HTML parsing and wrting with \DOMDocument()
     *      - The html and body tag are preventing strange p-tags while using LIBXML_HTML_NOIMPLIED
     *      - The doctype declaration allows us the usage of umlauts and special characters
     *
     * @param string $html
     * @return string
     */
    public static function wrapHtmlWithMainTags(string $html): string
    {
        return '<?xml encoding="utf-8" ?><html><body>' . $html . '</body></html>';
    }
}
