<?php
declare(strict_types=1);
namespace In2code\Instagram\Middleware;

use In2code\Instagram\Domain\Repository\InstagramRepository;
use In2code\Instagram\Domain\Repository\TokenRepository;
use In2code\Instagram\Exception\ApiConnectionException;
use In2code\Instagram\Exception\ConfigurationException;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface;
use TYPO3\CMS\Core\Http\RedirectResponse;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Class GetAuthenticationCode
 */
class GetAuthenticationCode implements MiddlewareInterface
{
    /**
     * @var TokenRepository|null
     */
    protected $tokenRepository = null;

    /**
     * @var InstagramRepository|null
     */
    protected $instagramRepository = null;

    /**
     * Change given "code" GET-parameter from instagram to a short-live and then a long-live token and save it to
     * database. If everything is persisted, a 301 redirect to home is done (better: Show a message instead?)
     *
     * @param ServerRequestInterface $request
     * @param RequestHandlerInterface $handler
     * @return ResponseInterface
     * @throws ConfigurationException
     * @throws ApiConnectionException
     */
    public function process(ServerRequestInterface $request, RequestHandlerInterface $handler): ResponseInterface
    {
        if ($this->isInstagramAuthentificationRedirect($request)) {
            $this->tokenRepository = GeneralUtility::makeInstance(TokenRepository::class);
            $this->instagramRepository = GeneralUtility::makeInstance(InstagramRepository::class);
            $code = $request->getQueryParams()['code'];
            $tokenRecord = $this->tokenRepository->findLatestEmptyToken();
            $result = $this->instagramRepository->getLongLiveTokenResult(
                $tokenRecord['app_id'],
                $tokenRecord['app_secret'],
                $tokenRecord['app_return_url'],
                $code
            );
            $this->tokenRepository->updateToken(
                $tokenRecord['username'],
                $result['access_token'],
                $result['expires_in'],
                $result['user_id']
            );
            return new RedirectResponse('/', 301);
        }
        return $handler->handle($request);
    }

    /**
     * @param ServerRequestInterface $request
     * @return bool
     */
    protected function isInstagramAuthentificationRedirect(ServerRequestInterface $request): bool
    {
        if (!empty($request->getQueryParams()['code'])) {
            $code = $request->getQueryParams()['code'];
            if (strlen($code) > 8) {
                return true;
            }
        }
        return false;
    }
}
