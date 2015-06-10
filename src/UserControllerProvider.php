<?php

namespace CultuurNet\UiTIDProvider;

use CultuurNet\UDB3\Symfony\JsonLdResponse;
use Silex\Application;
use Silex\ControllerCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class UserControllerProvider extends ControllerExtensionProvider
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
        $controllers = $this->getControllerCollection($app);

        /**
         * Returns info on the currently logged in user.
         */
        $controllers->get('/user', function (Request $request, Application $app) {
            /* @var \CultureFeed_User $user */
            $user = $app['uitid_current_user'];

            $response = JsonLdResponse::create()
                ->setData($user)
                ->setPrivate();

            return $response;
        });

        /**
         * Logout method.
         */
        $controllers->get('/logout', function (Request $request, Application $app) {
            /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
            $session = $app['session'];
            $session->invalidate();

            return new Response('Logged out');
        });

        return $controllers;
    }
}
