<?php

namespace CultuurNet\UiTIDProvider\User;

interface UserServiceInterface
{
    /**
     * @param string $id
     * @return User
     */
    public function getUser($id);
}
