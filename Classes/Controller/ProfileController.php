<?php
declare(strict_types=1);
namespace In2code\Instagram\Controller;

use In2code\Instagram\Domain\Repository\InstagramRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;
use TYPO3\CMS\Frontend\ContentObject\ContentObjectRenderer;

/**
 * Class ProfileController
 */
class ProfileController extends ActionController
{
    /**
     * @var InstagramRepository
     */
    protected $instagramRepository = null;

    /**
     * ProfileController constructor.
     * @param InstagramRepository $instagramRepository
     */
    public function __construct(InstagramRepository $instagramRepository)
    {
        $this->instagramRepository = $instagramRepository;
    }

    /**
     * @return void
     */
    public function showAction()
    {
        $rssFeed = $this->instagramRepository->findDataByUsername($this->settings['username']);
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
