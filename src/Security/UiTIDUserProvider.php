<?php

namespace CultuurNet\UiTIDProvider\Security;

use CultuurNet\UiTIDProvider\User\User;
use CultuurNet\UiTIDProvider\User\UserService;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;

class UiTIDUserProvider implements UserProviderInterface
{
    /**
     * @var UserService
     */
    protected $userService;

    /**
     * @param UserService $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @param $id
     * @return User|null
     */
    public function loadUserById($id)
    {
        return $this->userService->getUser($id);
    }

    /**
     * @inheritdoc
     */
    public function loadUserByUsername($username)
    {
        $user = $this->userService->getUserByUsername($username);

        if (is_null($user)) {
            throw new UsernameNotFoundException();
        }

        return $user;
    }

    /**
     * @inheritdoc
     */
    public function refreshUser(UserInterface $user)
    {
        if (!$this->supportsClass(get_class($user))) {
            throw new UnsupportedUserException();
        }

        return $this->loadUserByUsername($user->getUsername());
    }

    /**
     * @inheritdoc
     */
    public function supportsClass($class)
    {
        return $class === User::class;
    }
}
