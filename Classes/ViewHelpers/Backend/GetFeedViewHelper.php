<?php
declare(strict_types=1);
namespace In2code\Instagram\ViewHelpers\Backend;

use In2code\Instagram\Domain\Repository\FeedRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetFeedViewHelper
 * @noinspection PhpUnused
 */
class GetFeedViewHelper extends AbstractViewHelper
{
    /**
     * @return void
     */
    public function initializeArguments()
    {
        parent::initializeArguments();
        $this->registerArgument('flexForm', 'array', 'tt_content.pi_flexform as array', true);
    }

    /**
     * @return array
     */
    public function render(): array
    {
        /** @var FeedRepository $instagramRepository */
        $instagramRepository = GeneralUtility::makeInstance(FeedRepository::class);
        return $instagramRepository->findDataByUsername((string)$this->arguments['flexForm']['settings']['username']);
    }
}
