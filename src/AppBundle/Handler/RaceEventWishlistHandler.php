<?php

namespace AppBundle\Handler;

use AppBundle\Entity\RaceEvent;
use AppBundle\Entity\RaceEventWishlist;
use AppBundle\Entity\User;
use AppBundle\Model\APIResponse;
use AppBundle\Repository\RaceEventWishlistRepository;
use AppBundle\Repository\RaceEventRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Services\APIResponseBuilder;
use Symfony\Component\Form\FormFactoryInterface;

class RaceEventWishlistHandler
{
    /**
     * @var RaceEventRepository
     */
    private $raceEventRepository;

    /**
     * @var APIResponseBuilder
     */
    private $apiResponseBuilder;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var RaceEventWishlistRepository
     */
    private $raceEventWishlistRepository;

    /**
     * RaceEventWishlistHandler constructor.
     * @param RaceEventRepository $raceEventRepository
     * @param APIResponseBuilder $apiResponseBuilder
     * @param FormFactoryInterface $formFactory
     * @param UserRepository $userRepository
     * @param RaceEventWishlistRepository $raceEventWishlistRepository
     */
    public function __construct(
        RaceEventRepository $raceEventRepository,
        APIResponseBuilder $apiResponseBuilder,
        FormFactoryInterface $formFactory,
        UserRepository $userRepository,
        RaceEventWishlistRepository $raceEventWishlistRepository
    ) {
        $this->raceEventRepository = $raceEventRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->formFactory = $formFactory;
        $this->userRepository = $userRepository;
        $this->raceEventWishlistRepository = $raceEventWishlistRepository;
    }

    /**
     * @param string $id
     * @param string $userId
     *
     * @return APIResponse
     */
    public function handleCreate(string $id, string $userId)
    {
        /** @var RaceEvent $raceEvent */
        $raceEvent = $this->raceEventRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($raceEvent === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('RaceEvent not found');
        }

        /** @var User $user */
        $user = $this->userRepository->findOneBy([
            'id' => $userId,
        ]);
        if ($user === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('User not found');
        }

        $check = $this->raceEventWishlistRepository->findByRaceEventAndUser($raceEvent, $user);
        if ($check !== null) {
            return $this->apiResponseBuilder->buildBadRequestResponse('RaceEvent already in wishlist');
        }

        $raceEventWishlist = new RaceEventWishlist();
        $raceEventWishlist->setRaceEvent($raceEvent);
        $raceEventWishlist->setUser($user);

        $this->raceEventWishlistRepository->add($raceEventWishlist);
        $this->raceEventWishlistRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse(201);
    }

    /**
     * @param string $id
     * @param string $userId
     *
     * @return APIResponse
     */
    public function handleDelete(string $id, string $userId)
    {
        /** @var RaceEvent $raceEvent */
        $raceEvent = $this->raceEventRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($raceEvent === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('RaceEvent not found');
        }

        /** @var User $user */
        $user = $this->userRepository->findOneBy([
            'id' => $userId,
        ]);
        if ($user === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('User not found');
        }

        $raceEventWishlist = $this->raceEventWishlistRepository->findByRaceEventAndUser($raceEvent, $user);
        if ($raceEventWishlist === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('RaceEventWishlist Data not found');
        }

        $this->raceEventWishlistRepository->remove($raceEventWishlist);
        $this->raceEventWishlistRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse(204);
    }
}
