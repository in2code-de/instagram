<?php
declare(strict_types=1);
namespace In2code\Instagram\Command;

use In2code\Instagram\Domain\Repository\InstagramRepository;
use In2code\Instagram\Domain\Service\FetchFeed;
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
     * ImportFeedCommand constructor.
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->fetchFeed = GeneralUtility::makeInstance(FetchFeed::class);
        $this->instagramRepository = GeneralUtility::makeInstance(InstagramRepository::class);
    }

    /**
     * @return void
     */
    public function configure()
    {
        $this->setDescription('Import instagram feed');
        $this->addArgument('username', InputArgument::REQUIRED, 'Instagram username');
        $this->addArgument('limit', InputArgument::OPTIONAL, 'Limit for posts', 20);
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
            $limit = (int)$input->getArgument('limit');
            $feed = $this->fetchFeed->get($username, $limit);
            $this->instagramRepository->insert($username, $feed);
            $output->writeln(count($feed) . ' stories from ' . $username . ' stored into database');
            return 0;
        } catch (\Exception $exception) {
            $output->writeln('Feed could not be fetched from Instagram');
            $output->writeln($exception->getMessage());
            return 1605297993;
        }
    }
}
