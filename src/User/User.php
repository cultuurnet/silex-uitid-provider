<?php

namespace CultuurNet\UiTIDProvider\User;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\User\UserInterface;

class User extends \CultureFeed_User implements \JsonSerializable, UserInterface
{
    /**
     * (PHP 5 >= 5.4.0)
     * Specify data which should be serialized to JSON
     * @link http://php.net/manual/en/jsonserializable.jsonserialize.php
     * @return mixed data which can be serialized by json_encode,
     * which is a value of any type other than a resource.
     */
    public function jsonSerialize()
    {
        $data = (array) $this;

        // Unset the "following" property on the user, as it contains a recursive reference to the user
        // object itself, which makes it impossible to json_encode the user object.
        unset($data['following']);

        return $data;
    }

    /**
     * @param \CultureFeed_User $user
     * @return User|self
     */
    public static function fromCultureFeedUser(\CultureFeed_User $user)
    {
        $new = new self();

        $source = new \ReflectionObject($user);
        $properties = $source->getProperties();
        foreach ($properties as $propertyObject) {
            $property = $propertyObject->getName();
            $value = $propertyObject->getValue($user);

            $new->{$property} = $value;
        }

        return $new;
    }

    /**
     * @inheritdoc
     */
    public function getRoles()
    {
        return array('UITID_USER');
    }

    /**
     * @inheritdoc
     */
    public function getPassword()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    public function getSalt()
    {
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getUsername()
    {
        return $this->nick;
    }

    /**
     * @inheritdoc
     */
    public function eraseCredentials()
    {
    }
}
