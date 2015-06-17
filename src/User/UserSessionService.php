<?php

namespace CultuurNet\UiTIDProvider\User;

use CultuurNet\Auth\User as MinimalUserInfo;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class UserSessionService implements UserSessionServiceInterface
{
    /**
     * Name of the session variable that stores the user.
     */
    const USER_VARIABLE = 'culturefeed_user';

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param SessionInterface $session
     */
    public function __construct(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @param MinimalUserInfo $user
     */
    public function setMinimalUserInfo(MinimalUserInfo $user)
    {
        $this->session->set(self::USER_VARIABLE, $user);
    }

    /**
     * @return MinimalUserInfo|null
     */
    public function getMinimalUserInfo()
    {
        return $this->session->get(self::USER_VARIABLE);
    }

    public function logout()
    {
        $this->session->invalidate();
    }
}
