<?php

namespace CultuurNet\UiTIDProvider\User;

use Silex\Application;
use Silex\ControllerCollection;
use Silex\ControllerProviderInterface;

class UserControllerProvider implements ControllerProviderInterface
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
        $app['uitid_user_controller'] = $app->share(function (Application $app) {
            return new UserController(
                $app['uitid_user_service'],
                $app['uitid_user_session_service']
            );
        });

        /* @var ControllerCollection $controllers */
        $controllers = $app['controllers_factory'];

        $controllers->get('/user', 'uitid_user_controller:getUser');
        $controllers->get('/logout', 'uitid_user_controller:logout');

        return $controllers;
    }
}
