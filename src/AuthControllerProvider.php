<?php

namespace CultuurNet\UiTIDProvider;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class AuthControllerProvider implements ControllerProviderInterface
{
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

        /** @var \Symfony\Component\Routing\Generator\UrlGeneratorInterface $urlGenerator */
        $urlGenerator = $app['url_generator'];

        /** @var \CultuurNet\Auth\ServiceInterface $authService */
        $authService = $app['auth_service'];

        /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
        $session = $app['session'];

        $controllers->get(
            'culturefeed/oauth/connect',
            function (Request $request, Application $app) use ($urlGenerator, $authService, $session) {
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
                $session->set('culturefeed_tmp_token', $token);

                $authorizeUrl = $authService->getAuthorizeUrl($token);
                return new RedirectResponse($authorizeUrl);
            }
        );

        $controllers->get(
            'culturefeed/oauth/authorize',
            function (Request $request, Application $app) use ($urlGenerator, $authService, $session) {
                $query = $request->query;

                /** @var \CultuurNet\Auth\TokenCredentials $token */
                $token = $session->get('culturefeed_tmp_token');

                if ($query->get('oauth_token') == $token->getToken() && $query->get('oauth_verifier')) {
                    $user = $authService->getAccessToken($token, $query->get('oauth_verifier'));
                    $session->remove('culturefeed_tmp_token');
                    $session->set('culturefeed_user', $user);
                }

                if ($query->get('destination')) {
                    return new RedirectResponse($query->get('destination'));
                } else {
                    return new RedirectResponse($urlGenerator->generate($app['uitid_auth_default_destination']));
                }
            }
        )->bind('culturefeed.oauth.authorize');

        return $controllers;
    }
}
