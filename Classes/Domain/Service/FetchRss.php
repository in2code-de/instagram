<?php
declare(strict_types=1);
namespace In2code\Instagram\Domain\Service;

use In2code\Instagram\Exception\FetchCouldNotBeResolvedException;
use In2code\Instagram\Utility\ArrayUtility;
use In2code\Instagram\Utility\DomDocumentUtility;
use In2code\Instagram\Utility\FileUtility;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Http\Response;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FetchRss
 */
class FetchRss
{
    /**
     * @var string
     */
    protected $imageFolder = 'typo3temp/assets/tx_instagram/';

    /**
     * @var bool
     */
    protected $storeImages = true;

    /**
     * @param string $url
     * @return array
     * @throws FetchCouldNotBeResolvedException
     */
    public function fetch(string $url): array
    {
        $rssString = $this->fetchRss($url);
        $rss = ArrayUtility::convertRssXmlToArray(simplexml_load_string($rssString));
        $rss = $this->persistImages($rss);
        return $rss;
    }

    /**
     * @param string $url
     * @return string
     * @throws FetchCouldNotBeResolvedException
     */
    protected function fetchRss(string $url): string
    {
        $result = '';
        try {
            $additionalOptions = [
                'headers' => ['Cache-Control' => 'no-cache'],
                'allow_redirects' => false
            ];
            $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
            /** @var Response $response */
            $response = $requestFactory->request($url, 'GET', $additionalOptions);
            if ($response->getStatusCode() === 200) {
                if (strpos($response->getHeaderLine('Content-Type'), 'text/xml') === 0) {
                    $result = $response->getBody()->getContents();
                } else {
                    throw new FetchCouldNotBeResolvedException(
                        'Content from url has not a valid xml header',
                        1591347675
                    );
                }
            } else {
                throw new FetchCouldNotBeResolvedException(
                    'Wrong statuscode while trying to fetch url ' . htmlspecialchars($url),
                    1591347503
                );
            }
        } catch (\Exception $exception) {
            throw new FetchCouldNotBeResolvedException($exception->getMessage(), 1591347508);
        }
        return $result;
    }

    /**
     * Get image source from description and add this to the array. In addition store images locally.
     * @param array $rss
     * @return array
     * @throws FetchCouldNotBeResolvedException
     */
    protected function persistImages(array $rss): array
    {
        if ($this->storeImages === true) {
            $path = GeneralUtility::getFileAbsFileName($this->imageFolder);
            FileUtility::createFolderIfNotExists($path);

            if (!empty($rss['channel']['item'])) {
                foreach ($rss['channel']['item'] as &$item) {
                    $guid = $item['guid'];
                    $imageUrl = DomDocumentUtility::getFirstImageSourceFromString($item['description']);
                    $item['imageurl'] = $imageUrl;
                    $pathAndName = GeneralUtility::getFileAbsFileName($this->imageFolder) . $guid . '.jpg';
                    if (is_file($pathAndName) === false && $imageUrl !== '') {
                        $imageContent = $this->getImageContent($imageUrl);
                        GeneralUtility::writeFile($pathAndName, $imageContent, true);
                    }
                }
            }
        }
        return $rss;
    }

    /**
     * @param string $url
     * @return string
     * @throws FetchCouldNotBeResolvedException
     */
    protected function getImageContent(string $url): string
    {
        $content = '';
        try {
            $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
            /** @var Response $response */
            $response = $requestFactory->request($url, 'GET', ['headers' => ['Cache-Control' => 'no-cache']]);
            if ($response->getStatusCode() === 200) {
                $content = $response->getBody()->getContents();
            } else {
                throw new FetchCouldNotBeResolvedException('Image could not be fetched from ' . $url, 1588947571);
            }
        } catch (\Exception $exception) {
            throw new FetchCouldNotBeResolvedException($exception->getMessage(), 1588947539);
        }
        return $content;
    }
}
