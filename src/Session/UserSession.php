<?php

namespace CultuurNet\UiTIDProvider\Session;

use CultuurNet\Auth\TokenCredentials;
use CultuurNet\Auth\User;
use Symfony\Component\HttpFoundation\Session\Session;

class UserSession extends Session
{
    /**
     * Name of the session variable that stores the request token.
     */
    const REQUEST_TOKEN_VARIABLE = 'culturefeed_tmp_token';

    /**
     * Name of the session variable that stores the user.
     */
    const USER_VARIABLE = 'culturefeed_user';

    /**
     * @param TokenCredentials $token
     */
    public function setRequestToken(TokenCredentials $token)
    {
        $this->set(self::REQUEST_TOKEN_VARIABLE, $token);
    }

    /**
     * @return TokenCredentials
     */
    public function getRequestToken()
    {
        return $this->get(self::REQUEST_TOKEN_VARIABLE);
    }

    public function removeRequestToken()
    {
        $this->remove(self::REQUEST_TOKEN_VARIABLE);
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->set(self::USER_VARIABLE, $user);
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->get(self::USER_VARIABLE);
    }

    public function removeUser()
    {
        $this->remove(self::USER_VARIABLE);
    }
}
