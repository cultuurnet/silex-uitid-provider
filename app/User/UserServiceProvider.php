<?php

namespace CultuurNet\UiTIDProvider\User;

use Silex\Application;
use Silex\ServiceProviderInterface;

class UserServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['uitid_user_service'] = $app->share(function (Application $app) {
            return new UserService($app['culturefeed']);
        });

        $app['uitid_user_session_service'] = $app->share(function (Application $app) {
            return new UserSessionService($app['session']);
        });

        $app['uitid_user_session_data'] = $app->share(function (Application $app) {
            /* @var UserSessionService $userSessionService */
            $userSessionService = $app['uitid_user_session_service'];
            return $userSessionService->getMinimalUserInfo();
        });

        $app['uitid_user_session_data_complete'] = $app->share(function (Application $app) {
            /* @var UserSessionService $userSessionService */
            $userSessionService = $app['uitid_user_session_service'];
            return $userSessionService->getUser();
        });

        $app['uitid_user'] = $app->share(function (Application $app) {
            if (!is_null($app['uitid_user_session_data_complete'])) {
                return $app['uitid_user_session_data_complete'];
            }

            /* @var \Cultuurnet\Auth\User $userSessionData */
            $userSessionData = $app['uitid_user_session_data'];
            if (is_null($userSessionData)) {
                return null;
            }

            /* @var UserService $userService */
            $userService = $app['uitid_user_service'];
            $user = $userService->getUser($userSessionData->getId());

            /* @var UserSessionService $userSessionService */
            $userSessionService = $app['uitid_user_session_service'];
            $userSessionService->setUser($user);

            return $user;
        });
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
