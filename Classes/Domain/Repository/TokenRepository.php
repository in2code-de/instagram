<?php
declare(strict_types=1);
namespace In2code\Instagram\Domain\Repository;

use In2code\Instagram\Exception\ConfigurationException;
use In2code\Instagram\Utility\DatabaseUtility;
use In2code\Instagram\Utility\DateUtility;

/**
 * Class TokenRepository
 */
class TokenRepository
{
    const TABLE_NAME = 'tx_instagram_token';

    /**
     * Because in the first step an authentication code will be get from instagram without relation to a username
     * we need to add empty token records, that can be filled in a second step. Empty token will be generated when
     * opening the plugin in the backend (only if there is no valid token).
     *
     * @param string $username
     * @param string $appId
     * @param string $appSecret
     * @param string $appReturnUrl
     * @return void
     */
    public function addEmptyToken(string $username, string $appId, string $appSecret, string $appReturnUrl): void
    {
        $token = $this->findValidTokenByUsername($username);
        if ($token !== []) {
            return;
        }
        $this->removeTokensByUsername($username);
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME);
        $queryBuilder
            ->insert(self::TABLE_NAME)
            ->values([
                'username' => $username,
                'app_id' => $appId,
                'app_secret' => $appSecret,
                'app_return_url' => $appReturnUrl,
                'crdate' => time(),
            ])
            ->execute();
    }

    /**
     * Update an empty token record with token, expireDate and userId (if available)
     *
     * @param string $username
     * @param string $token
     * @param int $expires Seconds when token will expire
     * @param string $userId Must be string because of it's size
     * @return void
     * @throws ConfigurationException
     * @throws \Exception
     */
    public function updateToken(string $username, string $token, int $expires, $userId = '0'): void
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME);
        $uid = (int)$queryBuilder
            ->select('uid')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter($username))
            )
            ->orderBy('crdate', 'desc')
            ->execute()
            ->fetchColumn();
        if ($uid === 0) {
            throw new ConfigurationException('No empty token record found that can be extended', 1615748471);
        }

        $properties = [
            'token' => $token,
            'expire_date' => DateUtility::getExpireDateByExpireSeconds($expires)->getTimestamp()
        ];
        if ($userId > 0) {
            $properties += ['user_id' => $userId];
        }
        $connection = DatabaseUtility::getConnectionForTable(self::TABLE_NAME);
        $connection->update(self::TABLE_NAME, $properties, ['uid' => (int)$uid]);
    }

    /**
     * Search for the latest empty token to fill it when the instagram website redirects you back to your site
     *
     * @return array
     * @throws ConfigurationException
     */
    public function findLatestEmptyToken(): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME);
        $username = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('token', $queryBuilder->createNamedParameter('')),
                $queryBuilder->expr()->eq('expire_date', 0)
            )
            ->orderBy('crdate', 'desc')
            ->execute()
            ->fetch();
        if ($username === false) {
            throw new ConfigurationException('No empty token record found that can be filled', 1615750700);
        }
        return $username;
    }

    /**
     * Example return value:
     *  [
     *      'username' => 'in2code.de',
     *      'token' => 'abcdef',
     *      'expire_date' => 123465789,
     *      'app_id' => 12345,
     *      'app_secret' => 'abc1245455',
     *      'app_return_url' => 'https://www.in2code.de/',
     *      'crdate' => 987654321,
     *      'user_id' => 12345678
     *  ]
     *
     * @param string $username
     * @return array
     */
    public function findValidTokenByUsername(string $username): array
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME);
        $token = $queryBuilder
            ->select('*')
            ->from(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter($username)),
                $queryBuilder->expr()->neq('token', $queryBuilder->createNamedParameter('')),
                $queryBuilder->expr()->gt('expire_date', time())
            )
            ->orderBy('crdate', 'desc')
            ->execute()
            ->fetch();
        if ($token === false) {
            return [];
        }
        return $token;
    }

    /**
     * Delete tokens by given username
     *
     * @param string $username
     * @return void
     */
    protected function removeTokensByUsername(string $username): void
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME);
        $queryBuilder
            ->delete(self::TABLE_NAME)
            ->where(
                $queryBuilder->expr()->eq('username', $queryBuilder->createNamedParameter($username))
            )
            ->execute();
    }

    /**
     * @return void
     */
    public function truncateTokens(): void
    {
        DatabaseUtility::getConnectionForTable(self::TABLE_NAME)->truncate(self::TABLE_NAME);
    }
}
