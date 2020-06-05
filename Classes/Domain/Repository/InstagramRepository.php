<?php
declare(strict_types=1);
namespace In2code\Instagram\Domain\Repository;

use In2code\Instagram\Domain\Service\FetchRss;
use TYPO3\CMS\Core\Cache\CacheManager;
use TYPO3\CMS\Core\Cache\Frontend\FrontendInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class InstagramRepository
 */
class InstagramRepository
{
    /**
     * @var string
     */
    protected $cacheKey = 'instagram';

    /**
     * Default cache live time is 24h
     *
     * @var int
     */
    protected $cacheLifeTime = 86400;

    /**
     * @var FrontendInterface
     */
    protected $cacheInstance = null;

    /**
     * @var ContentObjectRenderer
     */
    protected $contentObject = null;

    /**
     * InstagramRepository constructor.
     * @param ContentObjectRenderer $contentObject
     */
    public function __construct(ContentObjectRenderer $contentObject)
    {
        $this->contentObject = $contentObject;
        $this->cacheInstance = GeneralUtility::makeInstance(CacheManager::class)->getCache($this->cacheKey);
    }

    /**
     * @param string $url
     * @return array
     */
    public function findByRssUrl(string $url): array
    {
        $rss = $this->getRssFeedFromCache();
        if ($rss === []) {
            $fetchProfile = GeneralUtility::makeInstance(FetchRss::class);
            $rss = $fetchProfile->fetch($url);
            $this->cacheRssFeed($rss);
        }
        return $rss;
    }

    /**
     * @param array $rssFeed
     * @return void
     */
    protected function cacheRssFeed(array $rssFeed): void
    {
        if ($rssFeed !== []) {
            $this->cacheInstance->set($this->getCacheIdentifier(), $rssFeed, [$this->cacheKey], $this->cacheLifeTime);
        }
    }

    /**
     * @return array
     */
    protected function getRssFeedFromCache(): array
    {
        $rssFeed = [];
        $rssFeedCache = $this->cacheInstance->get($this->getCacheIdentifier());
        if (!empty($rssFeedCache)) {
            $rssFeed = $rssFeedCache;
        }
        return $rssFeed;
    }

    /**
     * @return string
     */
    protected function getCacheIdentifier(): string
    {
        return md5($this->contentObject->data['uid'] . $this->cacheKey);
    }
}
