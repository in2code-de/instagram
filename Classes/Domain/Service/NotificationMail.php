<?php
declare(strict_types=1);
namespace In2code\Instagram\Domain\Service;

use TYPO3\CMS\Core\Mail\FluidEmail;
use TYPO3\CMS\Core\Mail\Mailer;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class NotificationMail
 */
class NotificationMail
{
    /**
     * @param string $receiverEmails
     * @param array $arguments
     * @param \Exception $exception
     * @return void
     */
    public function send(string $receiverEmails, array $arguments, \Exception $exception): void
    {
        foreach (GeneralUtility::trimExplode(',', $receiverEmails, true) as $email) {
            if (GeneralUtility::validEmail($email)) {
                $this->sendEmail($email, $arguments, $exception);
            }
        }
    }

    /**
     * @param string $email
     * @param array $arguments
     * @param \Exception $exception
     * @return void
     */
    protected function sendEmail(string $email, array $arguments, \Exception $exception): void
    {
        $message = 'Message: ' . $exception->getMessage() . ' (' . $exception->getCode() . ') / ';
        $message .= 'Arguments: ' . print_r($arguments, true);
        /** @var FluidEmail $email */
        $email = GeneralUtility::makeInstance(FluidEmail::class)
            ->to($email)
            ->subject('in2code/instagram failure')
            ->setTemplate('Default')
            ->assignMultiple([
                'headline' => 'in2code/instagram failure',
                'introduction' =>
                    'Failure while trying to import a feed from instagram.com. Please check your tasks manually.',
                'content' => $message
            ]);
        GeneralUtility::makeInstance(Mailer::class)->send($email);
    }
}
