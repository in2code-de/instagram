<?php
declare(strict_types=1);
namespace In2code\Instagram\Command;

use In2code\Instagram\Domain\Repository\TokenRepository;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RemoveTokensCommand
 */
class RemoveTokensCommand extends Command
{
    /**
     * @var TokenRepository|null
     */
    protected $tokenRepository = null;

    /**
     * ImportFeedCommand constructor.
     * @param string|null $name
     */
    public function __construct(string $name = null)
    {
        parent::__construct($name);
        $this->tokenRepository = GeneralUtility::makeInstance(TokenRepository::class);
    }
    /**
     * @return void
     */
    public function configure()
    {
        $this->setDescription('Really remove all tokens from database');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->tokenRepository->truncateTokens();
        $output->writeln('All tokens successfully removed');
        return 0;
    }
}
