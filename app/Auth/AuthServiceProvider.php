<?php

namespace CultuurNet\UiTIDProvider\Auth;

use Silex\Application;
use Silex\ServiceProviderInterface;

class AuthServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['uitid_auth_service'] = $app->share(
            function ($app) {
                return new AuthService(
                    $app['culturefeed.endpoint'],
                    $app['culturefeed_consumer_credentials'],
                    $app['session']
                );
            }
        );
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
