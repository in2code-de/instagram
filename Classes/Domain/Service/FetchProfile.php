<?php
declare(strict_types=1);
namespace In2code\Instagram\Domain\Service;

use In2code\Instagram\Exception\FetchCouldNotBeResolvedException;
use In2code\Instagram\Exception\HtmlCouldNotBeFetchedException;
use In2code\Instagram\Utility\ArrayUtility;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FetchProfile
 */
class FetchProfile
{
    /**
     * @param string $profileId
     * @return array
     * @throws FetchCouldNotBeResolvedException
     * @throws HtmlCouldNotBeFetchedException
     */
    public function getConfiguration(string $profileId): array
    {
        $configuration = $this->fetchFromInstagram($profileId);
        if (empty($configuration['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'])) {
            throw new FetchCouldNotBeResolvedException(
                'Json array structure changed? Could not get value edge_owner_to_timeline_media',
                1588171346
            );
        }
        return $configuration['entry_data']['ProfilePage'][0]['graphql']['user']['edge_owner_to_timeline_media']['edges'];
    }

    /**
     * @param string $profileId
     * @return array
     * @throws FetchCouldNotBeResolvedException
     * @throws HtmlCouldNotBeFetchedException
     */
    protected function fetchFromInstagram(string $profileId): array
    {
        $html = $this->getHtmlFromInstagramProfile($profileId);
        preg_match_all('~<script[^>]*>window._sharedData\s+=\s+(.+)<\/script>~U', $html, $result);
        if (empty($result[1][0]) === false || is_string($result[1][0]) === true) {
            $json = rtrim($result[1][0], ';');
            if (ArrayUtility::isJsonArray($json) === false) {
                throw new HtmlCouldNotBeFetchedException(
                    'Could not find a script tag with window._sharedData in HTML from instagram',
                    1588169250
                );
            }
            $configuration = json_decode($json, true);
        } else {
            throw new HtmlCouldNotBeFetchedException(
                'Could not find a script tag with window._sharedData in HTML from instagram',
                1588168267
            );
        }
        return $configuration;
    }

    /**
     * @param string $profileId
     * @return string
     * @throws FetchCouldNotBeResolvedException
     */
    public function getHtmlFromInstagramProfile(string $profileId): string
    {
        $result = '';
        try {
            $additionalOptions = [
                'headers' => ['Cache-Control' => 'no-cache'],
                'allow_redirects' => false,
                'cookies' => false,
            ];
            $requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
            $response = $requestFactory->request($this->getUrl($profileId), 'GET', $additionalOptions);
            if ($response->getStatusCode() === 200) {
                if (strpos($response->getHeaderLine('Content-Type'), 'text/html') === 0) {
                    $result = $response->getBody()->getContents();
                }
            } else {
                throw new FetchCouldNotBeResolvedException(
                    'Wrong statuscode while trying to fetch url ' . $this->getUrl($profileId),
                    1588165689
                );
            }
        } catch (\Exception $exception) {
            throw new FetchCouldNotBeResolvedException($exception->getMessage(), 1588165732);
        }
        return $result;
    }

    /**
     * @param string $profileId
     * @return string
     */
    protected function getUrl(string $profileId): string
    {
        return 'https://www.instagram.com/' . $profileId . '/';
    }
}
