<?php

namespace CultuurNet\UiTIDProvider\User;

use CultuurNet\UiTIDProvider\JsonAssertionTrait;

class UserTest extends \PHPUnit_Framework_TestCase
{
    use JsonAssertionTrait;

    /**
     * @test
     */
    public function it_implements_symfony_user_interface()
    {
        $user = new User();
        $user->nick = 'Foo';

        // Does nothing, but is a required method of UserInterface.
        $user->eraseCredentials();

        $this->assertEmpty($user->getPassword());
        $this->assertNull($user->getSalt());

        $this->assertEquals($user->nick, $user->getUsername());

        $this->assertContains('UITID_USER', $user->getRoles());
    }

    /**
     * @test
     */
    public function it_can_be_instantiated_from_a_culturefeed_user()
    {
        $cfUser = new \CultureFeed_User();

        $properties = [
            'id' => 1,
            'nick' => 'foo',
            'mbox' => 'foo@bar.com',
            'city' => 'Leuven',
            'country' => 'Belgium',
        ];

        foreach ($properties as $property => $value) {
            $cfUser->{$property} = $value;
        }

        $user = User::fromCultureFeedUser($cfUser);

        foreach ($properties as $property => $value) {
            $this->assertEquals($value, $user->{$property});
        }
    }

    /**
     * @test
     */
    public function it_can_be_safely_encoded_to_json()
    {
        $user = new User();
        $user->id = 1;
        $user->nick = 'foo';
        $user->mbox = 'foo@bar.com';

        $follower = new \CultureFeed_Pages_Follower();

        $page = new \CultureFeed_Cdb_Item_Page();
        $page->setId(10);
        $page->setName('Page');

        $follower->page = $page;
        $follower->user = $user;

        $user->following = array($follower);

        $encoded = json_encode($user);

        $this->assertJsonEquals($encoded, 'User/data/user.json');
    }
}
