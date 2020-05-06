<?php
declare(strict_types=1);
namespace In2code\Instagram\Domain\Service;

use In2code\Instagram\Exception\FetchCouldNotBeResolvedException;
use In2code\Instagram\Exception\HtmlCouldNotBeFetchedException;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class FetchProfile
 */
class FetchProfile
{
    /**
     * @var string
     */
    protected $feedUri = 'https://www.instagram.com/graphql/query/?query_id=17888483320059182&variables={%%22id%%22:%%22%d%%22,%%22first%%22:50}';

    /**
     * @var string
     */
    protected $profileUri = 'https://www.instagram.com/%s/?__a=1';

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    public function __construct(RequestFactory $requestFactory = null)
    {
        $this->requestFactory = $requestFactory ?: GeneralUtility::makeInstance(RequestFactory::class);
    }

    /**
     * @param string $username
     * @throws FetchCouldNotBeResolvedException
     * @throws HtmlCouldNotBeFetchedException
     * @return array
     */
    public function getConfiguration(string $username): array
    {
        $configuration = $this->fetchFromInstagram($username);
        if (empty($configuration['data']['user']['edge_owner_to_timeline_media']['edges'])) {
            throw new FetchCouldNotBeResolvedException(
                'Json array structure changed? Could not get value edge_owner_to_timeline_media',
                1588171346
            );
        }

        return $configuration['data']['user']['edge_owner_to_timeline_media']['edges'];
    }

    /**
     * @param string $username
     * @throws FetchCouldNotBeResolvedException
     * @throws HtmlCouldNotBeFetchedException
     * @return array
     */
    protected function fetchFromInstagram(string $username): array
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

        $feedUri = sprintf($this->feedUri, $profile['graphql']['user']['id']);
        $request = $this->requestFactory->request($feedUri);
        if ($request->getStatusCode() !== 200) {
            throw new FetchCouldNotBeResolvedException(
                'Could not fetch feed for "' . $username . '" on "' . $feedUri . '"',
                1588777238
            );
        }

        return json_decode($request->getBody()->getContents(), true);
    }
}
