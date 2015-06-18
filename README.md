# Silex UiTID provider

[![Build Status](https://travis-ci.org/cultuurnet/silex-uitid-provider.svg?branch=master)](https://travis-ci.org/cultuurnet/silex-uitid-provider)
[![Coverage Status](https://coveralls.io/repos/cultuurnet/silex-uitid-provider/badge.svg?branch=master)](https://coveralls.io/r/cultuurnet/silex-uitid-provider?branch=master)

Contains various Controller- and Service Providers for Silex projects to integrate UiTID authentication.

## Dependencies

You'll need the `Session` and `UrlGenerator` services provided by Silex:

	$app->register(new \Silex\Provider\SessionServiceProvider());
	$app->register(new \Silex\Provider\UrlGeneratorServiceProvider());
	
You will also need to register the `ServiceControllerServiceProvider`:

	$app->register(new Silex\Provider\ServiceControllerServiceProvider());
	
This service makes it possible to load controllers as if they are services, so you can use separate classes for your controllers outside of the ControllerProvider classes.

Lastly you will have to register the `CultureFeedServiceProvider`, with some additional configuration:

	$app->register(new \CultuurNet\UiTIDProvider\CultureFeed\CultureFeedServiceProvider(), array(
    	'culturefeed.endpoint' => 'http://example.com/,
    	'culturefeed.consumer.key' => 'example-consumer-key',
    	'culturefeed.consumer.secret' => 'example-consumer-secret',
	));
	
## UiTID Authentication

You will need to register the `AuthServiceProvider` like this:

	$app->register(new CultuurNet\UiTIDProvider\Auth\AuthServiceProvider());
	
And you will need to mount the `AuthControllerProvider` to a path of your liking:

	$app->mount(
		'culturefeed/oauth', 
		new \CultuurNet\UiTIDProvider\Auth\AuthControllerProvider()
	);
	
At this point, your visitors can authenticate if you redirect them to [http://your-website.com/culturefeed/oauth/connect?destination=http://your-website.com](http://your-website.com/culturefeed/oauth/connect?destination=http://your-website.com), where your-website.com should obviously be your own domain name.

After authentication, they will be redirected back to the URL set in the destination parameter. In this case, `http://your-website.com`.

## User info

You can access info for the current user, or other users, by registering the `UserServiceProvider`:

	$app->register(new CultuurNet\UiTIDProvider\User\UserServiceProvider());
	
This provides multiple services:

* `$app['uitid_user_service']`: An instance of `CultuurNet\UiTIDProvider\User\UserService`, which can return user data by id or username.
* `$app['uitid_user_session_service']`: An instance of `CultuurNet\UiTIDProvider\User\UserSessionService`, which can return minimal user info of the currently logged in user.
* `$app['uitid_user_session_data']`: An instance of `CultuurNet\Auth\User`, which contains the user id and access token. (Also known as the "minimal user info".)
* `$app['uitid_user']`: An instance of `CultuurNet\UiTIDProvider\User\User`, which contains all extra info of the currently logged in user.

Optionally, you can mount the `UserControllerProvider` to a path of your liking:

	$app->mount('uitid', new \CultuurNet\UiTIDProvider\User\UserControllerProvider());

This will provide the following paths (in this example prefixed with `uitid`):

* `uitid/user`: Returns data of the current user in JSON format.
* `uitid/logout`: Invalidates the current session and logs the user out.

	

