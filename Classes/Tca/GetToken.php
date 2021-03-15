<?php
declare(strict_types=1);
namespace In2code\Instagram\Tca;

use In2code\Instagram\Domain\Repository\TokenRepository;
use In2code\Instagram\Utility\ArrayUtility;
use TYPO3\CMS\Backend\Form\Element\AbstractFormElement;
use TYPO3\CMS\Core\Utility\GeneralUtility;
use TYPO3\CMS\Fluid\View\StandaloneView;

/**
 * Class GetToken
 * is called when the plugin is opened in the backend. This renders a message if the token is there and still valid and
 * also gives you a link to get a initial token. This script will also generate empty token records that can be filled
 * later with more information from a further API request.
 */
class GetToken extends AbstractFormElement
{
    /**
     * Note when a token is nearly expired - define the number of days
     *
     * @var int
     */
    protected $closeExpireDays = 7;

    /**
     * @var string
     */
    protected $template = 'EXT:instagram/Resources/Private/Templates/Tca/GetToken.html';

    /**
     * @var TokenRepository|null
     */
    protected $tokenRepository = null;

    /**
     * @return array
     */
    public function render()
    {
        $this->tokenRepository = GeneralUtility::makeInstance(TokenRepository::class);
        $this->createEmptyTokenRecord();
        $result = $this->initializeResultArray();
        $result['html'] = $this->getHtml();
        return $result;
    }

    /**
     * @return void
     */
    protected function createEmptyTokenRecord(): void
    {
        if ($this->isReadyToGetToken()) {
            $fields = ArrayUtility::cleanFlexFormArray($this->data['flexFormRowData']);
            $this->tokenRepository->addEmptyToken(
                $fields['username'],
                $fields['appId'],
                $fields['appSecret'],
                $fields['appReturnUrl']
            );
        }
    }

    /**
     * @return string
     */
    protected function getHtml(): string
    {
        /** @var StandaloneView $standaloneView */
        $standaloneView = GeneralUtility::makeInstance(StandaloneView::class);
        $standaloneView->setTemplatePathAndFilename(GeneralUtility::getFileAbsFileName($this->template));
        $standaloneView->assignMultiple([
            'fields' => ArrayUtility::cleanFlexFormArray($this->data['flexFormRowData']),
            'isReadyToGetToken' => $this->isReadyToGetToken(),
            'authenticationUrl' => $this->getAuthenticationUrl(),
            'isTokenValid' => $this->getToken() !== [],
            'tokenExpireDate' => $this->getTokenExpireDate(),
            'daysUntilExpiration' => $this->getDaysUntilExpiration(),
            'isDayUntilExpirationNear' => $this->getDaysUntilExpiration() < $this->closeExpireDays
        ]);
        return $standaloneView->render();
    }

    /**
     * @return int
     */
    protected function getDaysUntilExpiration(): int
    {
        return (int)$this->getTokenExpireDate()->diff(new \DateTime())->format('%a');
    }

    /**
     * Like:
     * "https://api.instagram.com/oauth/authorize
     *      ?client_id=123&redirect_uri=https://abc.de/&scope=user_profile,user_media&response_type=code"
     *
     * @return string
     */
    protected function getAuthenticationUrl(): string
    {
        $fields = ArrayUtility::cleanFlexFormArray($this->data['flexFormRowData']);
        $url = 'https://api.instagram.com/oauth/authorize?scope=user_profile,user_media&response_type=code';
        if (!empty($fields['appId'])) {
            $url .= '&client_id=' . $fields['appId'];
        }
        if (!empty($fields['appReturnUrl'])) {
            $url .= '&redirect_uri=' . $fields['appReturnUrl'];
        }
        return $url;
    }

    /**
     * @return \DateTime
     */
    protected function getTokenExpireDate(): \DateTime
    {
        $token = $this->getToken();
        $expireDate = 0;
        if (array_key_exists('expire_date', $token)) {
            $expireDate = $token['expire_date'];
        }
        return \DateTime::createFromFormat('U', (string)$expireDate);
    }

    /**
     * @return array
     */
    protected function getToken(): array
    {
        $fields = ArrayUtility::cleanFlexFormArray($this->data['flexFormRowData']);
        return $this->tokenRepository->findValidTokenByUsername($fields['username']);
    }

    /**
     * @return bool
     */
    protected function isReadyToGetToken(): bool
    {
        $fields = ArrayUtility::cleanFlexFormArray($this->data['flexFormRowData']);
        return !empty($fields['username']) && !empty($fields['appId'])
            && !empty($fields['appSecret']) && !empty($fields['appReturnUrl']);
    }
}
