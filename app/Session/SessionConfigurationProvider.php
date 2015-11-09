<?php

namespace CultuurNet\UiTIDProvider\Session;

use Silex\Application;
use Silex\ServiceProviderInterface;

/**
 * Provides a default session configuration for UiTID projects.
 *
 * Class SessionConfigurationProvider
 * @package CultuurNet\UiTIDProvider\Session
 */
class SessionConfigurationProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        /**
         * Keep the session for 1 year
         */
        $app['session.storage.options'] = [
            'cookie_lifetime' => 31536000
        ];
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
