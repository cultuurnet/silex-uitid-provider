<?php

namespace CultuurNet\UiTIDProvider\User;

use CultuurNet\UDB3\Symfony\JsonLdResponse;
use Symfony\Component\HttpFoundation\Response;

class UserController
{
    /**
     * @var UserServiceInterface
     */
    protected $userService;

    /**
     * @var UserSessionServiceInterface
     */
    protected $userSessionService;

    /**
     * @param UserServiceInterface $userService
     * @param UserSessionServiceInterface $userSessionService
     */
    public function __construct(
        UserServiceInterface $userService,
        UserSessionServiceInterface $userSessionService
    ) {
        $this->userService = $userService;
        $this->userSessionService = $userSessionService;
    }

    /**
     * @return JsonLdResponse
     */
    public function getUser()
    {
        $user = null;
        $userSessionData = $this->userSessionService->getActiveUser();

        if (!is_null($userSessionData)) {
            $user = $this->userService->getUser($userSessionData->getId());
        }

        return JsonLdResponse::create()
            ->setData($user)
            ->setPrivate();
    }

    /**
     * @return Response
     */
    public function logout()
    {
        $this->userSessionService->logout();
        return new Response();
    }
}
