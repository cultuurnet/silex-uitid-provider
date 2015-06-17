<?php

namespace CultuurNet\UiTIDProvider\Security;

use CultuurNet\Auth\TokenCredentials;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class UiTIDToken extends AbstractToken
{
    /**
     * @var TokenCredentials
     */
    protected $credentials;

    public function __construct(array $roles = array())
    {
        parent::__construct($roles);
        $this->setAuthenticated(count($roles) > 0);
    }

    /**
     * @param TokenCredentials $credentials
     */
    public function setCredentials(TokenCredentials $credentials)
    {
        $this->credentials = $credentials;
    }

    /**
     * @return TokenCredentials
     */
    public function getCredentials()
    {
        return $this->credentials;
    }
}
