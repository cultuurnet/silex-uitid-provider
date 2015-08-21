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
     * @return User|null
     */
    public function getUser($id)
    {
        try {
            $cfUser = $this->cultureFeed->getUser($id, self::INCLUDE_PRIVATE_FIELDS);

            // Cast to a User object that can be safely encoded to json.
            return User::fromCultureFeedUser($cfUser);
        } catch (\CultureFeed_ParseException $e) {
            return null;
        }
    }

    /**
     * @param $username
     * @return User|null
     */
    public function getUserByUsername($username)
    {
        try {
            $query = new \CultureFeed_SearchUsersQuery();
            $query->nick = $username;

            $results = $this->cultureFeed->searchUsers($query);
            $users = $results->objects;

            if (empty($users)) {
                return null;
            }

            $user = reset($users);

            return $this->getUser($user->id);
        } catch (\CultureFeed_ParseException $e) {
            return null;
        }
    }
}
