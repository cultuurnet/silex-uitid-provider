<?php

namespace CultuurNet\UiTIDProvider\Session;

use CultuurNet\Auth\TokenCredentials;
use CultuurNet\Auth\User;
use Symfony\Component\HttpFoundation\Session\Storage\MockArraySessionStorage;

class UserSessionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var UserSession
     */
    protected $session;

    /**
     * @var TokenCredentials
     */
    protected $token;

    /**
     * @var User
     */
    protected $user;

    public function setUp()
    {
        $this->session = new UserSession(new MockArraySessionStorage());

        $this->token = new TokenCredentials('token', 'secret');
        $this->user = new User('id', $this->token);
    }

    /**
     * @test
     */
    public function it_can_store_return_and_remove_the_request_token()
    {
        $this->session->setRequestToken($this->token);
        $this->assertEquals($this->token, $this->session->getRequestToken());

        $this->session->removeRequestToken();
        $this->assertNull($this->session->getRequestToken());
    }

    /**
     * @test
     */
    public function it_can_store_return_and_remove_the_user_info()
    {
        $this->session->setUser($this->user);
        $this->assertEquals($this->user, $this->session->getUser());

        $this->session->removeUser();
        $this->assertNull($this->session->getUser());
    }
}
