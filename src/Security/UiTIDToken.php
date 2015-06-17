<?php

namespace CultuurNet\UiTIDProvider\Security;

use CultuurNet\Auth\TokenCredentials;
use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;

class UiTIDToken extends AbstractToken
{
    public function __construct(array $roles = array())
    {
        parent::__construct($roles);
        $this->setAuthenticated(count($roles) > 0);
    }

    /**
     * @inheritdoc
     */
    public function getCredentials()
    {
        return '';
    }
}
