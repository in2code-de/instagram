<?php
declare(strict_types=1);
namespace In2code\Instagram\Command;

use In2code\Instagram\Domain\Repository\InstagramRepository;
use In2code\Instagram\Domain\Repository\TokenRepository;
use In2code\Instagram\Exception\ApiConnectionException;
use In2code\Instagram\Exception\ConfigurationException;
use In2code\Instagram\Utility\DateUtility;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class RefreshTokenCommand
 */
class RefreshTokenCommand extends Command
{
    /**
     * @var InstagramRepository|null
     */
    protected $instagramRepository = null;

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
        $this->instagramRepository = GeneralUtility::makeInstance(InstagramRepository::class);
        $this->tokenRepository = GeneralUtility::makeInstance(TokenRepository::class);
    }
    /**
     * @return void
     */
    public function configure()
    {
        $this->setDescription('Refresh token');
        $this->addArgument('username', InputArgument::REQUIRED, 'Instagram username');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws ApiConnectionException
     * @throws ConfigurationException
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        $result = $this->instagramRepository->refreshToken($input->getArgument('username'));
        $this->tokenRepository->updateToken(
            $input->getArgument('username'),
            $result['access_token'],
            $result['expires_in']
        );
        $output->writeln(
            'Token successfully updated and will be expire again on '
            . DateUtility::getExpireDateByExpireSeconds($result['expires_in'])->format('Y-m-d H:i')
        );
        return 0;
    }
}
