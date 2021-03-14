<?php
declare(strict_types=1);
namespace In2code\Instagram\Utility;

/**
 * Class DateUtility
 */
class DateUtility
{
    /**
     * @param int $expire
     * @return \DateTime
     * @throws \Exception
     */
    public static function getExpireDateByExpireSeconds(int $expire): \DateTime
    {
        $date = new \DateTime();
        $date->add(new \DateInterval('PT' . $expire . 'S'));
        return $date;
    }
}
