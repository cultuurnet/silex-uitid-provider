<?php

namespace CultuurNet\UiTIDProvider\User;

use CultuurNet\Auth\User;

interface UserSessionServiceInterface
{
    /**
     * @param User $user
     */
    public function setActiveUser(User $user);

    /**
     * @return User|null
     */
    public function getActiveUser();

    public function logout();
}
