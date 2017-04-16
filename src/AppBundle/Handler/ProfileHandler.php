<?php

namespace AppBundle\Handler;

use AppBundle\Model\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * RaceEvent handler.
 */
class ProfileHandler
{

    /**
     * @var APIResponseBuilder
     */
    private $apiResponseBuilder;

    /**
     * @var TokenStorage
     */
    private $tokenStorage;

    /**
     * @param APIResponseBuilder $apiResponseBuilder
     * @param TokenStorage $tokenStorage
     */
    public function __construct(
        APIResponseBuilder $apiResponseBuilder,
        TokenStorage $tokenStorage
    ) {
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @return APIResponse
     */
    public function handleGet()
    {
        /** @var TokenStorage $this */
        $token = $this->tokenStorage->getToken();
        $user = $token->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        $response = $this->apiResponseBuilder->buildSuccessResponse($user, 'user');
        $response->addResponseGroup('user');

        return $response;
    }
}
