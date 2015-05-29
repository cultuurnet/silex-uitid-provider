<?php

namespace CultuurNet\UiTIDProvider;

use Silex\Application;
use Silex\ServiceProviderInterface;

class AuthServiceProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $uitidConfig = $app['config']['uitid'];
        $consumerConfig = $uitidConfig['consumer'];

        $app['uitid_consumer_credentials'] = $app->share(
            function ($app) use ($consumerConfig) {
                return new \CultuurNet\Auth\ConsumerCredentials(
                    $consumerConfig['key'],
                    $consumerConfig['secret']
                );
            }
        );

        $app['auth_service'] = $app->share(
            function ($app) use ($uitidConfig) {
                return new \CultuurNet\Auth\Guzzle\Service(
                    $uitidConfig['base_url'],
                    $app['uitid_consumer_credentials']
                );
            }
        );
    }

    /**
     * Bootstraps the application.
     *
     * This method is called after all services are registered
     * and should be used for "dynamic" configuration (whenever
     * a service must be requested).
     */
    public function boot(Application $app)
    {
    }
}
