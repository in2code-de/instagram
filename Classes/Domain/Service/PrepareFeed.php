<?php
declare(strict_types=1);
namespace In2code\Instagram\Domain\Service;

use In2code\Instagram\Domain\Repository\InstagramRepository;
use In2code\Instagram\Exception\ApiConnectionException;
use In2code\Instagram\Utility\FileUtility;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class PrepareFeed
 */
class PrepareFeed
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
     * @var InstagramRepository
     */
    protected $instagramRepository = null;

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * GetFeed constructor.
     */
    public function __construct()
    {
        $this->instagramRepository = GeneralUtility::makeInstance(InstagramRepository::class);
        $this->requestFactory = GeneralUtility::makeInstance(RequestFactory::class);
    }

    /**
     * @param string $username
     * @return array
     * @throws ApiConnectionException
     */
    public function getByUsername(string $username): array
    {
        $feed = $this->instagramRepository->getFeed($username);
        $this->persistImages($feed);
        return $feed;
    }

    /**
     * @param array $feed
     * @return array
     * @throws ApiConnectionException
     */
    protected function persistImages(array $feed): array
    {
        if ($this->storeImages) {
            $path = GeneralUtility::getFileAbsFileName($this->imageFolder);
            FileUtility::createFolderIfNotExists($path);

            foreach ($feed['data'] as $item) {
                $imageContent = $this->getImageContent($item['thumbnail_url'] ?? $item['media_url']);
                $pathAndName = GeneralUtility::getFileAbsFileName($this->imageFolder) . $item['id'] . '.jpg';
                GeneralUtility::writeFile($pathAndName, $imageContent, true);
            }
        }
        return $feed;
    }

    /**
     * @param string $url
     * @return string
     * @throws ApiConnectionException
     */
    protected function getImageContent(string $url): string
    {
        try {
            $response = $this->requestFactory->request($url);
            if ($response->getStatusCode() === 200) {
                $content = $response->getBody()->getContents();
            } else {
                throw new ApiConnectionException('Image could not be fetched from ' . $url, 1615759345);
            }
        } catch (\Exception $exception) {
            throw new ApiConnectionException($exception->getMessage(), 1615759354);
        }
        return $content;
    }
}
