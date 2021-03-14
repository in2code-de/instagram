<?php
declare(strict_types=1);
namespace In2code\Instagram\Controller;

use In2code\Instagram\Domain\Repository\FeedRepository;
use In2code\Instagram\Domain\Repository\TokenRepository;
use TYPO3\CMS\Extbase\Mvc\Controller\ActionController;

/**
 * Class ProfileController
 */
class ProfileController extends ActionController
{
    /**
     * @var FeedRepository
     */
    protected $feedRepository = null;

    /**
     * @var TokenRepository
     */
    protected $tokenRepository = null;

    /**
     * ProfileController constructor.
     * @param FeedRepository $feedRepository
     * @param TokenRepository $tokenRepository
     */
    public function __construct(FeedRepository $feedRepository, TokenRepository $tokenRepository)
    {
        $this->feedRepository = $feedRepository;
        $this->tokenRepository = $tokenRepository;
    }

    /**
     * @return void
     */
    public function showAction()
    {
        $feed = $this->feedRepository->findDataByUsername((string)$this->settings['username']);
        $this->view->assignMultiple([
            'data' => $this->configurationManager->getContentObject()->data,
            'feed' => $feed,
            'token' => $this->tokenRepository->findValidTokenByUsername((string)$this->settings['username'])
        ]);
    }
}
