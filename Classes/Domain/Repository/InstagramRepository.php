<?php
declare(strict_types=1);
namespace In2code\Instagram\Domain\Repository;

use In2code\Instagram\Utility\DatabaseUtility;

/**
 * Class InstagramRepository
 */
class InstagramRepository
{
    const TABLE_NAME = 'tx_instagram_feed';

    /**
     * @param string $username
     * @return array
     */
    public function findByUsername(string $username): array
    {
        return [];
    }

    /**
     * @param string $username
     * @param array $feed
     * @return void
     */
    public function insert(string $username, array $feed): void
    {
        $this->deleteByUsername($username);
        $this->insertByUsername($username, $feed);
    }

    /**
     * @param string $username
     * @return void
     */
    protected function deleteByUsername(string $username): void
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
     * @param string $username
     * @param array $feed
     * @return void
     */
    protected function insertByUsername(string $username, array $feed): void
    {
        $queryBuilder = DatabaseUtility::getQueryBuilderForTable(self::TABLE_NAME);
        $queryBuilder
            ->insert(self::TABLE_NAME)
            ->values([
                'username' => $username,
                'data' => json_encode($feed),
                'import_date' => time()
            ])
            ->execute();
    }
}
