<?php

namespace CultuurNet\UiTIDProvider\Security;

use CultuurNet\UiTIDProvider\User\UserService;
use CultuurNet\UiTIDProvider\User\UserServiceInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Security\Core\Authentication\Provider\AuthenticationProviderInterface;
use Symfony\Component\Security\Core\Authentication\SimplePreAuthenticatorInterface;
use Symfony\Component\Security\Core\Authentication\Token\PreAuthenticatedToken;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\Security\Core\Exception\BadCredentialsException;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationFailureHandlerInterface;

class UiTIDAuthenticator implements AuthenticationProviderInterface, AuthenticationFailureHandlerInterface
{
    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @param UserServiceInterface $userService
     */
    public function __construct(UserServiceInterface $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @inheritdoc
     */
    public function authenticate(TokenInterface $token)
    {
        $userId = $token->getUser();
        $user = $this->userService->getUser($userId);

        if (is_null($user)) {
            throw new AuthenticationException(sprintf('User with id %s does not exist.', $userId));
        }

        $token = new UiTIDToken($user->getRoles());
        $token->setUser($user);
        $token->setCredentials($token->getCredentials());

        return $token;
    }

    /**
     * @inheritdoc
     */
    public function supports(TokenInterface $token)
    {
        return $token instanceof UiTIDToken;
    }

    /**
     * @inheritdoc
     */
    public function onAuthenticationFailure(Request $request, AuthenticationException $exception)
    {
        return new Response('Access denied.', Response::HTTP_FORBIDDEN);
    }
}
