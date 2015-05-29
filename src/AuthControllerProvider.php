<?php

namespace CultuurNet\UiTIDProvider;

use CultuurNet\Auth\ServiceInterface;
use CultuurNet\Auth\TokenCredentials;
use CultuurNet\Auth\User;
use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class AuthControllerProvider implements ControllerProviderInterface
{
    /**
     * Name of the session variable that stores the request token.
     */
    const SESSION_REQUEST_TOKEN_VARIABLE = 'culturefeed_tmp_token';

    /**
     * Name of the session variable that stores the user.
     */
    const SESSION_USER_VARIABLE = 'culturefeed_user';

    /**
     * @var ServiceInterface
     */
    protected $authService;

    /**
     * @var Session
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
     * @param Session $session
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function __construct(
        ServiceInterface $authService,
        Session $session,
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
     * @param TokenCredentials $token
     */
    protected function setSessionRequestToken(TokenCredentials $token)
    {
        $this->session->set(self::SESSION_REQUEST_TOKEN_VARIABLE, $token);
    }

    /**
     * @return TokenCredentials
     */
    protected function getSessionRequestToken()
    {
        return $this->session->get(self::SESSION_REQUEST_TOKEN_VARIABLE);
    }

    protected function removeSessionRequestToken()
    {
        $this->session->remove(self::SESSION_REQUEST_TOKEN_VARIABLE);
    }

    /**
     * @param User $user
     */
    protected function setSessionUser(User $user)
    {
        $this->session->set(self::SESSION_USER_VARIABLE, $user);
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
            'culturefeed/oauth/connect',
            function (Request $request, Application $app) use ($controllerProvider) {
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
                $controllerProvider->setSessionRequestToken($token);

                $authorizeUrl = $authService->getAuthorizeUrl($token);
                return new RedirectResponse($authorizeUrl);
            }
        );

        $controllers->get(
            'culturefeed/oauth/authorize',
            function (Request $request, Application $app) use ($controllerProvider) {
                $urlGenerator = $controllerProvider->urlGenerator;
                $authService = $controllerProvider->authService;

                $query = $request->query;
                $token = $this->getSessionRequestToken();

                if ($query->get('oauth_token') == $token->getToken() && $query->get('oauth_verifier')) {
                    $user = $authService->getAccessToken($token, $query->get('oauth_verifier'));

                    $controllerProvider->removeSessionRequestToken();
                    $controllerProvider->setSessionUser($user);
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
