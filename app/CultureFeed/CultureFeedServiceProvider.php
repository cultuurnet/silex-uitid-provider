<?php

namespace CultuurNet\UiTIDProvider\CultureFeed;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\TokenCredentials;
use CultuurNet\UiTIDProvider\User\UserSessionService;
use Silex\Application;
use Silex\ServiceProviderInterface;

class CultureFeedServiceProvider implements ServiceProviderInterface
{
    /**
     * @param Application $app
     */
    public function register(Application $app)
    {
        $app['culturefeed_token_credentials'] = $app->share(
            function (Application $app) {
                /* @var UserSessionService $userSessionService */
                $userSessionService = $app['uitid_user_session_service'];
                $user = $userSessionService->getActiveUser();
                if (!is_null($user)) {
                    return $user->getTokenCredentials();
                } else {
                    return null;
                }
            }
        );

        $app['culturefeed_consumer_credentials'] = $app->share(
            function (Application $app) {
                return new ConsumerCredentials(
                    $app['culturefeed.consumer.key'],
                    $app['culturefeed.consumer.secret']
                );
            }
        );

        $app['culturefeed'] = $app->share(
            function (Application $app) {
                return new \CultureFeed($app['culturefeed_oauth_client']);
            }
        );

        $app['culturefeed_oauth_client'] = $app->share(function (Application $app) {
            /* @var ConsumerCredentials $consumerCredentials */
            $consumerCredentials = $app['culturefeed_consumer_credentials'];

            /* @var TokenCredentials $tokenCredentials */
            $tokenCredentials = $app['culturefeed_token_credentials'];

            $userCredentialsToken = null;
            $userCredentialsSecret = null;
            if ($tokenCredentials) {
                $userCredentialsToken = $tokenCredentials->getToken();
                $userCredentialsSecret = $tokenCredentials->getSecret();
            }

            $oauthClient = new \CultureFeed_DefaultOAuthClient(
                $consumerCredentials->getKey(),
                $consumerCredentials->getSecret(),
                $userCredentialsToken,
                $userCredentialsSecret
            );
            $oauthClient->setEndpoint($app['culturefeed.endpoint']);
        });
    }

    /**
     * @param Application $app
     */
    public function boot(Application $app)
    {
    }
}
