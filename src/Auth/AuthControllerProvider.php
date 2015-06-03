<?php

namespace CultuurNet\UiTIDProvider\Auth;

use CultuurNet\Auth\ServiceInterface;
use CultuurNet\Auth\TokenCredentials;
use CultuurNet\Auth\User;
use CultuurNet\UiTIDProvider\Session\UserSession;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthControllerProvider implements ControllerProviderInterface
{
    /**
     * @var ServiceInterface
     */
    protected $authService;

    /**
     * @var UserSession
     */
    protected $session;

    /**
     * @var UrlGeneratorInterface
     */
    protected $urlGenerator;

    /**
     * @var string
     */
    protected $defaultDestination;

    /**
     * @param ServiceInterface $authService
     * @param UserSession $session
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        ServiceInterface $authService,
        UserSession $session,
        UrlGeneratorInterface $urlGenerator,
        $defaultDestination = null
    ) {
        $this->authService = $authService;
        $this->session = $session;
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
        $controllerProvider = $this;

        $controllers->get(
            '/connect',
            function (Request $request, Application $app) use ($controllerProvider) {
                $session = $controllerProvider->session;
                $urlGenerator = $controllerProvider->urlGenerator;
                $authService = $controllerProvider->authService;

                $callback_url_params = array();
                if ($request->query->get('destination')) {
                    $callback_url_params['destination'] = $request->query->get('destination');
                }

                $callback_url = $urlGenerator->generate(
                    'culturefeed.oauth.authorize',
                    $callback_url_params,
                    $urlGenerator::ABSOLUTE_URL
                );

                $token = $authService->getRequestToken($callback_url);
                $session->setRequestToken($token);

                $authorizeUrl = $authService->getAuthorizeUrl($token);
                return new RedirectResponse($authorizeUrl);
            }
        );

        $controllers->get(
            '/authorize',
            function (Request $request, Application $app) use ($controllerProvider) {
                $session = $controllerProvider->session;
                $urlGenerator = $controllerProvider->urlGenerator;
                $authService = $controllerProvider->authService;

                $query = $request->query;
                $token = $session->getRequestToken();

                if ($query->get('oauth_token') == $token->getToken() && $query->get('oauth_verifier')) {
                    $user = $authService->getAccessToken($token, $query->get('oauth_verifier'));

                    $session->removeRequestToken();
                    $session->setUser($user);
                }

                if ($query->get('destination')) {
                    return new RedirectResponse($query->get('destination'));
                } else {
                    return new RedirectResponse($urlGenerator->generate($this->defaultDestination));
                }
            }
        )->bind('culturefeed.oauth.authorize');

        return $controllers;
    }
}
