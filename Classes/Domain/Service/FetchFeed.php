<?php
declare(strict_types=1);
namespace In2code\Instagram\Domain\Service;

use In2code\Instagram\Exception\FetchCouldNotBeResolvedException;
use In2code\Instagram\Utility\FileUtility;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FetchFeed
 */
class FetchFeed
{
    /**
     * @var string
     */
    protected $feedUri = 'https://www.instagram.com/graphql/query/?query_id=17888483320059182&variables={%%22id%%22:%%22%d%%22,%%22first%%22:%d}';

    /**
     * @var string
     */
    protected $profileUri = 'https://www.instagram.com/%s/?__a=1';

    /**
     * @var string
     */
    protected $imageFolder = 'typo3temp/assets/tx_instagram/';

    /**
     * @var bool
     */
    protected $storeImages = true;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * FetchFeed constructor.
     * @param RequestFactory|null $requestFactory
     */
    public function __construct(RequestFactory $requestFactory = null)
    {
        $this->requestFactory = $requestFactory ?: GeneralUtility::makeInstance(RequestFactory::class);
    }

    /**
     * @param string $username
     * @param int $limit
     * @return array
     * @throws FetchCouldNotBeResolvedException
     */
    public function get(string $username, int $limit): array
    {
        $configuration = $this->fetchFromInstagram($username, $limit);
        if (empty($configuration['data']['user']['edge_owner_to_timeline_media']['edges'])) {
            throw new FetchCouldNotBeResolvedException(
                'Json array structure changed? Could not get value edge_owner_to_timeline_media',
                1588171346
            );
        }

        $feed = $configuration['data']['user']['edge_owner_to_timeline_media']['edges'];
        $this->persistImages($feed);
        return $feed;
    }

    /**
     * @param string $username
     * @param int $limit
     * @return array
     * @throws FetchCouldNotBeResolvedException
     */
    protected function fetchFromInstagram(string $username, int $limit): array
    {
        $profileUri = sprintf($this->profileUri, $username);
        $request = $this->requestFactory->request($profileUri);
        if ($request->getStatusCode() !== 200) {
            throw new FetchCouldNotBeResolvedException(
                'Could not fetch profile for "' . $username . '" on "' . $profileUri . '"',
                1588777142
            );
        }

        $profile = json_decode($request->getBody()->getContents(), true);

        $feedUri = sprintf($this->feedUri, $profile['graphql']['user']['id'], $limit);
        $request = $this->requestFactory->request($feedUri);
        if ($request->getStatusCode() !== 200) {
            throw new FetchCouldNotBeResolvedException(
                'Could not fetch feed for "' . $username . '" on "' . $feedUri . '"',
                1588777238
            );
        }

        return json_decode($request->getBody()->getContents(), true);
    }

    /**
     * @param array $configuration
     * @return array
     * @throws FetchCouldNotBeResolvedException
     */
    protected function persistImages(array $configuration): array
    {
        if ($this->storeImages) {
            $path = GeneralUtility::getFileAbsFileName($this->imageFolder);
            FileUtility::createFolderIfNotExists($path);

            foreach ($configuration as $item) {
                $shortcode = $item['node']['shortcode'];
                $imageContent = $this->getImageContent($item['node']['display_url']);
                $pathAndName = GeneralUtility::getFileAbsFileName($this->imageFolder) . $shortcode . '.jpg';
                GeneralUtility::writeFile($pathAndName, $imageContent, true);
            }
        }
        return $configuration;
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
            $response = $this->requestFactory->request($url);
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
