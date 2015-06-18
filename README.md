# Silex UiTID provider

[![Build Status](https://travis-ci.org/cultuurnet/silex-uitid-provider.svg?branch=master)](https://travis-ci.org/cultuurnet/silex-uitid-provider)
[![Coverage Status](https://coveralls.io/repos/cultuurnet/silex-uitid-provider/badge.svg?branch=master)](https://coveralls.io/r/cultuurnet/silex-uitid-provider?branch=master)

Contains various Controller- and Service Providers for Silex projects to integrate UiTID authentication.

## 0. Dependencies

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
	
## 1. UiTID Authentication

You will need to register the `AuthServiceProvider` and `UserServiceProvider` like this:

	$app->register(new CultuurNet\UiTIDProvider\Auth\AuthServiceProvider());
	$app->register(new CultuurNet\UiTIDProvider\User\UserServiceProvider());
	
And you will need to mount the `AuthControllerProvider` to a path of your liking:

	$app->mount(
		'culturefeed/oauth', 
		new \CultuurNet\UiTIDProvider\Auth\AuthControllerProvider()
	);
	
At this point, your visitors can authenticate if you redirect them to [http://your-website.com/culturefeed/oauth/connect?destination=http://your-website.com](http://your-website.com/culturefeed/oauth/connect?destination=http://your-website.com), where your-website.com should obviously be your own domain name.

After authentication, they will be redirected back to the URL set in the destination parameter. In this case, `http://your-website.com`.

## 2. User info

You can access info for the current user, or other users, by using the following services provided by the `UserServiceProvider` that you registered in step 1:

* **`$app['uitid_user_session_service']`**: An instance of `CultuurNet\UiTIDProvider\User\UserSessionService`, which can return minimal user info of the currently logged in user.
* **`$app['uitid_user_session_data']`**: An instance of `CultuurNet\Auth\User`, which contains the user id and access token. (Also known as the "minimal user info".)
* **`$app['uitid_user_service']`**: An instance of `CultuurNet\UiTIDProvider\User\UserService`, which can return user data by id or username.
* **`$app['uitid_user']`**: An instance of `CultuurNet\UiTIDProvider\User\User`, which contains all extra info of the currently logged in user.

Optionally, you can mount the `UserControllerProvider` to a path of your liking:

	$app->mount('uitid', new \CultuurNet\UiTIDProvider\User\UserControllerProvider());

This will provide the following paths (in this example prefixed with `uitid`):

* `uitid/user`: Returns data of the current user in JSON format.
* `uitid/logout`: Invalidates the current session and logs the user out.

## 3. Restricting access to paths for non-authenticated users.

You can easily restrict access to paths for non-authenticated users by registering the `SecurityServiceProvider` and `UiTIDSecurityServiceProvider`:

	$app->register(new \Silex\Provider\SecurityServiceProvider());
	$app->register(new \CultuurNet\UiTIDProvider\Security\UiTIDSecurityServiceProvider());
	
Afterwards you'll have to configure the firewall settings. Make sure to allow access to the paths that you mounted in step 1, use the `uitid` authenticator, and use the `$app['uitid_firewall_user_provider']` as the user provider.

Here's an example of a valid firewall configuration:

	$app['security.firewalls'] = array(
   		'unsecured' => array(
        	'pattern' => '^/culturefeed/oauth',
    	),
    	'secured' => array(
       	 	'pattern' => '^.*$',
        	'uitid' => true,
        	'users' => $app['uitid_firewall_user_provider'],
    	),
	);
	
This example will only allow access to the paths beginning wih `/culturefeed/oauth` until the user is logged in. All other paths will return a response with status code 403.
	
More info on firewall configuration can be found in the [Silex documentation](http://silex.sensiolabs.org/doc/providers/security.html).




	

