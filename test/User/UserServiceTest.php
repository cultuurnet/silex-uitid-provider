<?php

namespace CultuurNet\UiTIDProvider\User;

class UserServiceTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var \CultureFeed|\PHPUnit_Framework_MockObject_MockObject
     */
    protected $cultureFeed;

    /**
     * @var UserService
     */
    protected $service;

    public function setUp()
    {
        $oauthClient = new \CultureFeed_DefaultOAuthClient('key', 'secret');
        $this->cultureFeed = $this->getMock(\CultureFeed::class, [], [$oauthClient]);

        $this->service = new UserService($this->cultureFeed);
    }

    /**
     * @test
     */
    public function it_can_return_a_user_by_id()
    {
        $cfUser = new \CultureFeed_User();
        $cfUser->id = 1;
        $cfUser->nick = 'foo';
        $cfUser->mbox = 'foo@bar.com';

        $this->cultureFeed->expects($this->once())
            ->method('getUser')
            ->with(1, UserService::INCLUDE_PRIVATE_FIELDS)
            ->willReturn($cfUser);

        $user = $this->service->getUser(1);

        $this->assertInstanceOf(User::class, $user);
    }

    /**
     * @test
     */
    public function it_returns_null_when_a_user_can_not_be_found_by_id()
    {
        $this->cultureFeed->expects($this->once())
            ->method('getUser')
            ->with(1, UserService::INCLUDE_PRIVATE_FIELDS)
            ->willThrowException(new \CultureFeed_ParseException('error'));

        $user = $this->service->getUser(1);

        $this->assertNull($user);
    }

    /**
     * @test
     */
    public function it_can_return_a_user_by_username()
    {
        $id = 1;
        $username = 'foo';

        $query = new \CultureFeed_SearchUsersQuery();
        $query->nick = $username;

        $result = new \CultureFeed_SearchUser();
        $result->id = $id;
        $result->nick = $username;

        $resultSet = new \CultureFeed_ResultSet();
        $resultSet->total = 1;
        $resultSet->objects = array($result);

        $this->cultureFeed->expects($this->once())
            ->method('searchUsers')
            ->with($query)
            ->willReturn($resultSet);

        $cfUser = new \CultureFeed_User();
        $cfUser->id = $id;
        $cfUser->nick = $username;

        $this->cultureFeed->expects($this->once())
            ->method('getUser')
            ->with($id)
            ->willReturn($cfUser);

        $user = $this->service->getUserByUsername($username);

        $this->assertInstanceOf(User::class, $user);
        $this->assertEquals($id, $user->id);
        $this->assertEquals($username, $user->getUsername());
    }

    /**
     * @test
     */
    public function it_returns_null_when_a_user_can_not_be_found_by_username()
    {
        $username = 'foo';

        $query = new \CultureFeed_SearchUsersQuery();
        $query->nick = $username;

        $resultSet = new \CultureFeed_ResultSet();

        $this->cultureFeed->expects($this->once())
            ->method('searchUsers')
            ->with($query)
            ->willReturn($resultSet);

        $user = $this->service->getUserByUsername($username);
        $this->assertNull($user);
    }

    /**
     * @test
     */
    public function it_returns_null_when_a_parse_exception_occurs_when_searching_by_username()
    {
        $username = 'foo';

        $query = new \CultureFeed_SearchUsersQuery();
        $query->nick = $username;

        $this->cultureFeed->expects($this->once())
            ->method('searchUsers')
            ->with($query)
            ->willThrowException(new \CultureFeed_ParseException('error'));

        $user = $this->service->getUserByUsername($username);

        $this->assertNull($user);
    }
}
