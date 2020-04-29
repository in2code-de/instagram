<?php
declare(strict_types=1);
namespace In2code\Instagram\Controller;

use In2code\Instagram\Domain\Repository\InstagramRepository;
use In2code\Instagram\Exception\FetchCouldNotBeResolvedException;
use In2code\Instagram\Exception\HtmlCouldNotBeFetchedException;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class ProfileController
 */
class ProfileController extends ActionController
{
    /**
     * @return void
     * @throws FetchCouldNotBeResolvedException
     * @throws HtmlCouldNotBeFetchedException
     */
    public function showAction()
    {
        $instagramRepository = GeneralUtility::makeInstance(InstagramRepository::class);
        $configuration = $instagramRepository->findByProfileId($this->settings['profileId']);
        \TYPO3\CMS\Extbase\Utility\DebuggerUtility::var_dump($configuration, 'in2code ' . __CLASS__ . ':' . __LINE__);
        die(__CLASS__ . ':' . __LINE__);
    }
}
