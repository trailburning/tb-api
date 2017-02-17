<?php

namespace AppBundle\Handler;

use AppBundle\Entity\RaceEvent;
use AppBundle\Entity\RaceEventDoing;
use AppBundle\Entity\User;
use AppBundle\Model\APIResponse;
use AppBundle\Repository\RaceEventDoingRepository;
use AppBundle\Repository\RaceEventRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Services\APIResponseBuilder;
use Symfony\Component\Form\FormFactoryInterface;

class RaceEventDoingHandler
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
     * @var RaceEventDoingRepository
     */
    private $raceEventDoingRepository;

    /**
     * RaceEventDoingHandler constructor.
     * @param RaceEventRepository $raceEventRepository
     * @param APIResponseBuilder $apiResponseBuilder
     * @param FormFactoryInterface $formFactory
     * @param UserRepository $userRepository
     * @param RaceEventDoingRepository $raceEventDoingRepository
     */
    public function __construct(
        RaceEventRepository $raceEventRepository,
        APIResponseBuilder $apiResponseBuilder,
        FormFactoryInterface $formFactory,
        UserRepository $userRepository,
        RaceEventDoingRepository $raceEventDoingRepository
    ) {
        $this->raceEventRepository = $raceEventRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->formFactory = $formFactory;
        $this->userRepository = $userRepository;
        $this->raceEventDoingRepository = $raceEventDoingRepository;
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

        $check = $this->raceEventDoingRepository->findByRaceEventAndUser($raceEvent, $user);
        if ($check !== null) {
            return $this->apiResponseBuilder->buildBadRequestResponse('RaceEvent already in doing');
        }

        $raceEventDoing = new RaceEventDoing();
        $raceEventDoing->setRaceEvent($raceEvent);
        $raceEventDoing->setUser($user);

        $this->raceEventDoingRepository->add($raceEventDoing);
        $this->raceEventDoingRepository->store();

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

        $raceEventDoing = $this->raceEventDoingRepository->findByRaceEventAndUser($raceEvent, $user);
        if ($raceEventDoing === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('RaceEventDoing Data not found');
        }

        $this->raceEventDoingRepository->remove($raceEventDoing);
        $this->raceEventDoingRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse(204);
    }
}
