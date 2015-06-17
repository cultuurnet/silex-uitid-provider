<?php

namespace CultuurNet\UiTIDProvider\Auth;

use CultuurNet\Auth\ConsumerCredentials;
use CultuurNet\Auth\TokenCredentials;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class AuthServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var TokenCredentials
     */
    protected $requestToken;

    /**
     * @var Session|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $session;

    /**
     * @var AuthService
     */
    protected $service;

    public function setUp()
    {
        $this->session = new Session(new MockArraySessionStorage());

        $this->requestToken = new TokenCredentials('token', 'secret');

        $this->service = new AuthService(
            'http://example.com',
            new ConsumerCredentials('key', 'secret'),
            $this->session
        );
    }

    /**
     * @test
     */
    public function it_can_store_retrieve_and_remove_the_request_token()
    {
        // Request token is null by default.
        $this->assertNull($this->service->getStoredRequestToken());

        // Store an actual request token.
        $this->service->storeRequestToken($this->requestToken);

        // Make sure we get the same request token back.
        $this->assertEquals(
            $this->requestToken,
            $this->service->getStoredRequestToken()
        );

        // Remove the request token.
        $this->service->removeStoredRequestToken();

        // Make sure the request token is null again.
        $this->assertNull($this->service->getStoredRequestToken());
    }
}
