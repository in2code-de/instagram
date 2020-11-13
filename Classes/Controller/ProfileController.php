<?php
declare(strict_types=1);
namespace In2code\Instagram\Controller;

use In2code\Instagram\Domain\Repository\InstagramRepositoryOld;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

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
        $instagramRepository = GeneralUtility::makeInstance(InstagramRepositoryOld::class, $this->getContentObject());
        $rssFeed = $instagramRepository->findByRssUrl($this->settings['url']);
        $this->view->assignMultiple([
            'data' => $this->getContentObject()->data,
            'feed' => $rssFeed
        ]);
    }

    /**
     * @return ContentObjectRenderer
     */
    protected function getContentObject(): ContentObjectRenderer
    {
        return $this->configurationManager->getContentObject();
    }
}
