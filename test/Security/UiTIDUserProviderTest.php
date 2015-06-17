<?php

namespace CultuurNet\UiTIDProvider\Security;

use CultuurNet\UiTIDProvider\User\User;
use CultuurNet\UiTIDProvider\User\UserServiceInterface;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;

class UiTIDUserProviderTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var User
     */
    protected $user;

    /**
     * @var UserServiceInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userService;

    /**
     * @var UiTIDUserProvider
     */
    protected $userProvider;

    public function setUp()
    {
        $this->user = new User();
        $this->user->id = 1;
        $this->user->nick = 'foo';
        $this->user->city = 'Leuven';

        $this->userService = $this->getMock(UserServiceInterface::class);
        $this->userProvider = new UiTIDUserProvider($this->userService);
    }

    /**
     * @test
     */
    public function it_supports_only_uitid_users()
    {
        $this->assertTrue($this->userProvider->supportsClass(User::class));
        $this->assertFalse($this->userProvider->supportsClass(UserInterface::class));
    }

    /**
     * @test
     */
    public function it_queries_the_user_service_to_get_a_user_by_username()
    {
        $this->userService->expects($this->once())
            ->method('getUserByUsername')
            ->with($this->user->nick)
            ->willReturn($this->user);

        $this->assertEquals($this->user, $this->userProvider->loadUserByUsername($this->user->nick));
    }

    /**
     * @test
     */
    public function it_throws_an_exception_when_a_user_can_not_be_loaded()
    {
        $this->userService->expects($this->once())
            ->method('getUserByUsername')
            ->with($this->user->nick)
            ->willReturn(null);

        $this->setExpectedException(UsernameNotFoundException::class);
        $this->userProvider->loadUserByUsername($this->user->nick);
    }

    /**
     * @test
     */
    public function it_can_refresh_a_user()
    {
        $updatedUser = $this->user;
        $updatedUser->city = 'Herent';

        $this->userService->expects($this->once())
            ->method('getUserByUsername')
            ->with($this->user->nick)
            ->willReturn($updatedUser);

        $this->assertEquals(
            $updatedUser,
            $this->userProvider->refreshUser($this->user)
        );
    }

    /**
     * @test
     */
    public function it_throws_an_exception_if_refreshing_an_unsupported_user()
    {
        $user = $this->getMock(UserInterface::class);
        $this->setExpectedException(UnsupportedUserException::class);
        $this->userProvider->refreshUser($user);
    }
}
