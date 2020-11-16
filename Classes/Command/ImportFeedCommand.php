<?php
declare(strict_types=1);
namespace In2code\Instagram\Command;

use In2code\Instagram\Domain\Repository\InstagramRepository;
use In2code\Instagram\Domain\Service\FetchFeed;
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
     * @var FetchFeed
     */
    protected $fetchFeed = null;

    /**
     * @var InstagramRepository
     */
    protected $instagramRepository = null;

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
        $this->fetchFeed = GeneralUtility::makeInstance(FetchFeed::class);
        $this->instagramRepository = GeneralUtility::makeInstance(InstagramRepository::class);
        $this->notificationMail = GeneralUtility::makeInstance(NotificationMail::class);
    }

    /**
     * @return void
     */
    public function configure()
    {
        $this->setDescription('Import instagram feed');
        $this->addArgument('username', InputArgument::REQUIRED, 'Instagram username for the feed import');
        $this->addArgument('limit', InputArgument::OPTIONAL, 'How many posts should be imported?', 20);
        $this->addArgument('sessionid', InputArgument::OPTIONAL, 'Optional: Valid sessionid (see documentation)', '');
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
            $username = $input->getArgument('username');
            $feed = $this->fetchFeed->get(
                $username,
                (int)$input->getArgument('limit') === 0 ? 20 : (int)$input->getArgument('limit'),
                $input->getArgument('sessionid')
            );
            $this->instagramRepository->insert($username, $feed);
            $output->writeln(count($feed) . ' stories from ' . $username . ' stored into database');
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
