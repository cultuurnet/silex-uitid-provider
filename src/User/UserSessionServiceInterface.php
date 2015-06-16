<?php

namespace CultuurNet\UiTIDProvider\User;

use CultuurNet\Auth\User as MinimalUserInfo;

interface UserSessionServiceInterface
{
    /**
     * @param MinimalUserInfo $user
     */
    public function setActiveUser(MinimalUserInfo $user);

    /**
     * @return MinimalUserInfo|null
     */
    public function getActiveUser();

    public function logout();
}
