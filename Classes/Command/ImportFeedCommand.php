<?php
declare(strict_types=1);
namespace In2code\Instagram\Command;

use In2code\Instagram\Domain\Repository\FeedRepository;
use In2code\Instagram\Domain\Service\PrepareFeed;
use In2code\Instagram\Domain\Service\NotificationMail;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class ImportFeedCommand
 */
class ImportFeedCommand extends Command
{
    /**
     * @var PrepareFeed
     */
    protected $prepareFeed = null;

    /**
     * @var FeedRepository
     */
    protected $feedRepository = null;

    /**
     * @var NotificationMail
     */
    protected $notificationMail = null;

    /**
     * ImportFeedCommand constructor.
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->prepareFeed = GeneralUtility::makeInstance(PrepareFeed::class);
        $this->feedRepository = GeneralUtility::makeInstance(FeedRepository::class);
        $this->notificationMail = GeneralUtility::makeInstance(NotificationMail::class);
    }

    /**
     * @return void
     */
    public function configure()
    {
        $this->setDescription('Import instagram feed');
        $this->addArgument('username', InputArgument::REQUIRED, 'Instagram username for the feed import');
        $this->addArgument(
            'receivers',
            InputArgument::OPTIONAL,
            'Optional: Notify receivers on failures (commaseparated emails)',
            ''
        );
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $feed = $this->prepareFeed->getByUsername($input->getArgument('username'));
            $this->feedRepository->insert($input->getArgument('username'), $feed);
            $output->writeln(
                count($feed['data']) . ' stories from ' . $input->getArgument('username') . ' stored into database'
            );
            return 0;
        } catch (\Exception $exception) {
            $output->writeln('Feed could not be fetched from Instagram');
            $output->writeln('Reason: ' . $exception->getMessage());
            if ($input->getArgument('receivers') !== '') {
                $this->notificationMail->send($input->getArgument('receivers'), $input->getArguments(), $exception);
            }
            return 1605297993;
        }
    }
}
