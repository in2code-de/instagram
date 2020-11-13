<?php
declare(strict_types=1);
namespace In2code\Instagram\ViewHelpers\Backend;

use In2code\Instagram\Domain\Repository\InstagramRepository;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3Fluid\Fluid\Core\ViewHelper\AbstractViewHelper;

/**
 * Class GetFeedsViewHelper
 * @noinspection PhpUnused
 */
class GetFeedsViewHelper extends AbstractViewHelper
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
        /** @var InstagramRepository $instagramRepository */
        $instagramRepository = GeneralUtility::makeInstance(InstagramRepository::class);
        return $instagramRepository->findDataByUsername($this->arguments['flexForm']['settings']['username']);
    }
}
