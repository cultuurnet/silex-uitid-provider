<?php

namespace CultuurNet\UiTIDProvider\Session;

use CultuurNet\Auth\User;
use Silex\Application;
use Silex\Provider\SessionServiceProvider;

class UserSessionServiceProvider extends SessionServiceProvider
{
    public function register(Application $app)
    {
        parent::register($app);

        $serviceProvider = $this;

        $app['session'] = $app->share(function ($app) {
            if (!isset($app['session.storage'])) {
                if ($app['session.test']) {
                    $app['session.storage'] = $app['session.storage.test'];
                } else {
                    $app['session.storage'] = $app['session.storage.native'];
                }
            }
            return new UserSession($app['session.storage']);
        });

        $app['uitid_current_user'] = $app->share(
            function ($app) use ($serviceProvider) {
                /* @var UserSession $session */
                $session = $app['session'];
                $minimalUserData = $session->getUser();

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
     * @return \CultureFeed_DefaultOAuthClient
     */
    protected function getOAuthClient(Application $app, User $minimalUserData)
    {
        /* @var \CultuurNet\Auth\ConsumerCredentials $consumerCredentials */
        $consumerCredentials = $app['uitid_consumer_credentials'];
        $tokenCredentials = $minimalUserData->getTokenCredentials();

        $oathClient = new \CultureFeed_DefaultOAuthClient(
            $consumerCredentials->getKey(),
            $consumerCredentials->getSecret(),
            $tokenCredentials->getToken(),
            $tokenCredentials->getSecret()
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
}
