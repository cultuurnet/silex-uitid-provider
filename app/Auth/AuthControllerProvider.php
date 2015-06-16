<?php

namespace CultuurNet\UiTIDProvider\Auth;

use CultuurNet\UiTIDProvider\User\UserSessionServiceInterface;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthControllerProvider implements ControllerProviderInterface
{
    /**
     * @var AuthServiceInterface
     */
    protected $authService;

    /**
     * @var UserSessionServiceInterface
     */
    protected $userSessionService;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var string
     */
    protected $defaultDestination;

    /**
     * @param AuthServiceInterface $authService
     * @param UserSessionServiceInterface $userSessionService
     * @param UrlGeneratorInterface $urlGenerator
     * @param string|null $defaultDestination
     */
    public function __construct(
        AuthServiceInterface $authService,
        UserSessionServiceInterface $userSessionService,
        UrlGeneratorInterface $urlGenerator,
        $defaultDestination = null
    ) {
        $this->authService = $authService;
        $this->userSessionService = $userSessionService;
        $this->urlGenerator = $urlGenerator;

        if (is_null($defaultDestination)) {
            $this->defaultDestination = '/';
        } else {
            $this->defaultDestination = $defaultDestination;
        }
    }

    /**
     * Returns routes to connect to the given application.
     *
     * @param Application $app An Application instance
     *
     * @return ControllerCollection A ControllerCollection instance
     */
    public function connect(Application $app)
    {
        $controllers = $app['controllers_factory'];

        $controllers->get(
            '/connect',
            function (Request $request, Application $app) {
                $callback_url_params = array();
                if ($request->query->get('destination')) {
                    $callback_url_params['destination'] = $request->query->get('destination');
                }

                $callback_url = $this->urlGenerator->generate(
                    'culturefeed.oauth.authorize',
                    $callback_url_params,
                    UrlGeneratorInterface::ABSOLUTE_URL
                );

                $token = $this->authService->getRequestToken($callback_url);
                $this->authService->storeRequestToken($token);

                $authorizeUrl = $this->authService->getAuthorizeUrl($token);
                return new RedirectResponse($authorizeUrl);
            }
        );

        $controllers->get(
            '/authorize',
            function (Request $request, Application $app) {
                $query = $request->query;
                $token = $this->authService->getStoredRequestToken();

                if ($query->get('oauth_token') == $token->getToken() && $query->get('oauth_verifier')) {
                    $user = $this->authService->getAccessToken($token, $query->get('oauth_verifier'));

                    $this->authService->removeStoredRequestToken();
                    $this->userSessionService->setActiveUser($user);
                }

                if ($query->get('destination')) {
                    return new RedirectResponse($query->get('destination'));
                } else {
                    return new RedirectResponse($this->urlGenerator->generate($this->defaultDestination));
                }
            }
        )->bind('culturefeed.oauth.authorize');

        return $controllers;
    }
}
