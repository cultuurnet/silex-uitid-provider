<?php

namespace CultuurNet\UiTIDProvider\User;

class UserService implements UserServiceInterface
{
    /**
     * Include private fields when returning user data.
     */
    const INCLUDE_PRIVATE_FIELDS = true;

    /**
     * @var \CultureFeed
     */
    protected $cultureFeed;

    /**
     * @param \CultureFeed $cultureFeed
     */
    public function __construct(\CultureFeed $cultureFeed)
    {
        $this->cultureFeed = $cultureFeed;
    }

    /**
     * @param string $id
     * @return User
     */
    public function getUser($id)
    {
        $cfUser = $this->cultureFeed->getUser($id, self::INCLUDE_PRIVATE_FIELDS);

        // Cast to a User object that can be safely json encoded.
        return User::fromCultureFeedUser($cfUser);
    }
}
