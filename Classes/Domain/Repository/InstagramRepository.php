<?php
declare(strict_types=1);
namespace In2code\Instagram\Domain\Repository;

use In2code\Instagram\Exception\ApiConnectionException;
use In2code\Instagram\Exception\ConfigurationException;
use TYPO3\CMS\Core\Http\RequestFactory;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class InstagramRepository
 * to read values from Instagram API
 */
class InstagramRepository
{
    protected $redirectUri = 'https://www.in2code.de/';

    /**
     * @var RequestFactory
     */
    protected $requestFactory;

    /**
     * @var TokenRepository|null
     */
    protected $tokenRepository = null;

    /**
     * InstagramRepository constructor.
     * @param RequestFactory|null $requestFactory
     * @param TokenRepository|null $tokenRepository
     */
    public function __construct(RequestFactory $requestFactory = null, TokenRepository $tokenRepository = null)
    {
        $this->requestFactory = $requestFactory ?: GeneralUtility::makeInstance(RequestFactory::class);
        $this->tokenRepository = $tokenRepository ?: GeneralUtility::makeInstance(TokenRepository::class);
    }

    /**
     * Example return value:
     *  [
     *      'access_token' => 'xyzabc9876', // long live access token (different to short live access token)
     *      'expires_in' => 12345678, // seconds from now on
     *      'user_id' => 12345678
     *  ]
     *
     * @param string $appId
     * @param string $appSecret
     * @param string $appReturnUrl
     * @param string $code
     * @return array
     * @throws ApiConnectionException
     */
    public function getLongLiveTokenResult(string $appId, string $appSecret, string $appReturnUrl, string $code): array
    {
        $tokenShortLiveResult = $this->getShortLiveTokenResult($appId, $appSecret, $appReturnUrl, $code);
        $url = 'https://graph.instagram.com/access_token?grant_type=ig_exchange_token&client_secret='
            . $appSecret . '&access_token=' . $tokenShortLiveResult['access_token'];
        $request = $this->requestFactory->request($url);
        if ($request->getStatusCode() !== 200) {
            throw new ApiConnectionException('Could not get long-live token', 1615752607);
        }
        $result = json_decode($request->getBody()->getContents(), true);
        if (empty($result['access_token']) || empty($result['expires_in'])) {
            throw new ApiConnectionException('Result does not contain access_token or expires_in key', 1615752616);
        }
        return $result + ['user_id' => $tokenShortLiveResult['user_id']];
    }

    /**
     * Example return value:
     *  [
     *      'access_token' => 'newT0ken',
     *      'expires_in' => 123456
     *  ]
     *
     * @param string $username
     * @return array
     * @throws ApiConnectionException
     * @throws ConfigurationException
     */
    public function refreshToken(string $username): array
    {
        $tokenRecord = $this->tokenRepository->findValidTokenByUsername($username);
        if ($tokenRecord === []) {
            throw new ConfigurationException(
                'No valid token found that can be refreshed for user ' . $username,
                1615754772
            );
        }
        $url = 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token='
            . $tokenRecord['token'];
        $request = $this->requestFactory->request($url);
        if ($request->getStatusCode() !== 200) {
            throw new ApiConnectionException('Could not refresh token', 1615754880);
        }
        $result = json_decode($request->getBody()->getContents(), true);
        if (empty($result['access_token']) || empty($result['expires_in'])) {
            throw new ApiConnectionException('Result does not contain access_token or expires_in key', 1615755173);
        }
        return $result;
    }

    /**
     * Example return value:
     *  [
     *      'data' => [
     *          [
     *              'id' => 65465465,
     *              'username' => 'in2code.de',
     *              'caption' => 'nice image',
     *              'media_type' => 'IMAGE',
     *              'media_url' => 'https://cdninstagram.com...',
     *              'permalink' => 'https://instagram.com/abc...',
     *              'timestamp' => '2021-03-12T14:27:57+0000'
     *          ],
     *          [
     *              'id' => 65315461,
     *              'username' => 'in2code.de',
     *              'caption' => 'nice image 2',
     *              'media_type' => 'IMAGE',
     *              'media_url' => 'https://cdninstagram.com...',
     *              'permalink' => 'https://instagram.com/abc...',
     *              'timestamp' => '2021-03-12T14:27:55+0000'
     *          ]
     *      ]
     *  ]
     *
     * @param string $username
     * @return array
     * @throws ApiConnectionException
     * @throws ConfigurationException
     */
    public function getFeed(string $username): array
    {
        $tokenRecord = $this->tokenRepository->findValidTokenByUsername($username);
        if (empty($tokenRecord['user_id']) || empty($tokenRecord['token'])) {
            throw new ConfigurationException('No valid token record found', 1615767816);
        }
        $url = 'https://graph.instagram.com/' . $tokenRecord['user_id'] . '/media/'
            . '?fields=media,caption,media_type,media_url,permalink,thumbnail_url,timestamp,username,'
            . 'children&access_token=' . $tokenRecord['token'];
        $request = $this->requestFactory->request($url);
        if ($request->getStatusCode() !== 200) {
            throw new ApiConnectionException('Could not read the feed', 1615757725);
        }
        $result = json_decode($request->getBody()->getContents(), true);
        if (empty($result['data'][0]['id']) || empty($result['data'][0]['media_url'])
            || empty($result['data'][0]['permalink'])) {
            throw new ApiConnectionException('Result does not contain expected keys', 1615757825);
        }
        return $result;
    }

    /**
     * Example return value:
     *  [
     *      'access_token' => 'abcdef1234', // short live access token
     *      'user_id' => 12345678
     *  ]
     *
     * @param string $appId
     * @param string $appSecret
     * @param string $appReturnUrl
     * @param string $code
     * @return array
     * @throws ApiConnectionException
     */
    protected function getShortLiveTokenResult(
        string $appId,
        string $appSecret,
        string $appReturnUrl,
        string $code
    ): array {
        $url = 'https://api.instagram.com/oauth/access_token';
        $options = [
            'form_params' => [
                'client_id' => $appId,
                'client_secret' => $appSecret,
                'redirect_uri' => $appReturnUrl,
                'code' => $code,
                'grant_type' => 'authorization_code'
            ]
        ];
        $request = $this->requestFactory->request($url, 'POST', $options);
        if ($request->getStatusCode() !== 200) {
            throw new ApiConnectionException('Could not get short-live token', 1615751508);
        }
        $result = json_decode($request->getBody()->getContents(), true, 512, JSON_BIGINT_AS_STRING);
        if (empty($result['access_token']) || empty($result['user_id'])) {
            throw new ApiConnectionException('Result does not contain access_token or user_id key', 1615752470);
        }
        return $result;
    }
}
