<?php

namespace CultuurNet\UiTIDProvider\User;

use CultuurNet\Auth\TokenCredentials;
use CultuurNet\Auth\User as MinimalUserInfo;
use CultuurNet\UiTIDProvider\JsonAssertionTrait;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class UserControllerTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @var UserController
     */
    protected $controller;

    /**
     * @var UserService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userService;

    /**
     * @var UserSessionService|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $userSessionService;

    public function setUp()
    {
        $this->userService = $this->getMock(UserServiceInterface::class);
        $this->userSessionService = $this->getMock(UserSessionServiceInterface::class);

        $this->controller = new UserController(
            $this->userService,
            $this->userSessionService
        );
    }

    /**
     * @test
     */
    public function it_responds_the_active_users_data()
    {
        $id = 1;
        $credentials = new TokenCredentials('token', 'secret');
        $minimalUserInfo = new MinimalUserInfo($id, $credentials);

        $this->userSessionService->expects($this->once())
            ->method('getMinimalUserInfo')
            ->willReturn($minimalUserInfo);

        $user = new User();
        $user->id = 1;
        $user->nick = 'foo';
        $user->mbox = 'foo@bar.com';

        $this->userService->expects($this->once())
            ->method('getUser')
            ->with($id)
            ->willReturn($user);

        $response = $this->controller->getUser();

        $this->assertInstanceOf(JsonResponse::class, $response);
        $this->assertJsonEquals($response->getContent(), 'User/data/user.json');
    }

    /**
     * @test
     */
    public function it_responds_404_if_there_is_no_active_user()
    {
        $this->userSessionService->expects($this->once())
            ->method('getMinimalUserInfo')
            ->willReturn(null);

        $response = $this->controller->getUser();

        $this->assertEquals($response->getStatusCode(), Response::HTTP_NOT_FOUND);
    }

    /**
     * @test
     */
    public function it_invalidates_the_session_when_logging_out()
    {
        $this->userSessionService->expects($this->once())
            ->method('logout');

        $response = $this->controller->logout();

        $this->assertEquals($response->getStatusCode(), Response::HTTP_OK);
    }
}
