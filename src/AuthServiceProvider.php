<?php

namespace CultuurNet\UiTIDProvider;

use CultuurNet\Auth\User;
use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Session\Session;

class AuthServiceProvider implements ServiceProviderInterface
{
    /**
     * @var Session
     */
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    /**
     * @return User
     */
    protected function getMinimalUserData()
    {
        return $this->session->get(AuthControllerProvider::SESSION_USER_VARIABLE);
    }

    /**
     * @return \CultureFeed_DefaultOAuthClient
     */
    protected function getOAuthClient(Application $app, User $minimalUserData = null)
    {
        if (is_null($minimalUserData)) {
            $minimalUserData = $this->getMinimalUserData();
        }

        $userToken = null;
        $userSecret = null;
        if (!is_null($minimalUserData)) {
            $tokenCredentials = $minimalUserData->getTokenCredentials();
            $userToken = $tokenCredentials->getToken();
            $userSecret = $tokenCredentials->getSecret();
        }

        /* @var \CultuurNet\Auth\ConsumerCredentials $consumerCredentials */
        $consumerCredentials = $app['uitid_consumer_credentials'];

        $oathClient = new \CultureFeed_DefaultOAuthClient(
            $consumerCredentials->getKey(),
            $consumerCredentials->getSecret(),
            $userToken,
            $userSecret
        );
        $oathClient->setEndpoint($app['uitid.base_url']);
        return $oathClient;
    }

    /**
     * @return \CultureFeed
     */
    protected function getCultureFeed(Application $app, User $minimalUserData = null)
    {
        $oathClient = $this->getOAuthClient($app, $minimalUserData);
        return new \CultureFeed($oathClient);
    }

    /**
     * Registers services on the given app.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     */
    public function register(Application $app)
    {
        $serviceProvider = $this;

        $app['uitid_consumer_credentials'] = $app->share(
            function ($app) {
                return new \CultuurNet\Auth\ConsumerCredentials(
                    $app['uitid.consumer.key'],
                    $app['uitid.consumer.secret']
                );
            }
        );

        $app['auth_service'] = $app->share(
            function ($app) {
                return new \CultuurNet\Auth\Guzzle\Service(
                    $app['uitid.base_url'],
                    $app['uitid_consumer_credentials']
                );
            }
        );

        $app['current_user'] = $app->share(
            function ($app) use ($serviceProvider) {
                $minimalUserData = $serviceProvider->getMinimalUserData();

                if ($minimalUserData) {
                    $cf = $serviceProvider->getCultureFeed($app, $minimalUserData);

                    try {
                        $private = true;
                        $user = $cf->getUser($minimalUserData->getId(), $private);

                        // Unset the "following" property on the user, as it contains a recursive reference to the user
                        // object itself, which makes it impossible to json_encode the user object.
                        unset($user->following);

                        return $user;
                    } catch (\Exception $e) {
                        return null;
                    }
                } else {
                    return null;
                }
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
