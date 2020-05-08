<?php
declare(strict_types=1);
namespace In2code\Instagram\Controller;

use In2code\Instagram\Domain\Repository\InstagramRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class ProfileController
 */
class ProfileController extends ActionController
{
    /**
     * @return void
     */
    public function showAction()
    {
        $instagramRepository = GeneralUtility::makeInstance(InstagramRepository::class);
        $configuration = $instagramRepository->findByProfileId($this->settings['profileId']);
        $this->view->assignMultiple([
            'data' => $this->configurationManager->getContentObject()->data,
            'feed' => $configuration
        ]);
    }
}
