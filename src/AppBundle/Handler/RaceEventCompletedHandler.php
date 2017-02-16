<?php

namespace AppBundle\Handler;

use AppBundle\Entity\RaceEvent;
use AppBundle\Entity\RaceEventCompleted;
use AppBundle\Entity\User;
use AppBundle\Repository\RaceEventCompletedRepository;
use AppBundle\Repository\RaceEventRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Services\APIResponseBuilder;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;

class RaceEventCompletedHandler
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
     * @var RaceEventCompletedRepository
     */
    private $raceEventCompletedRepository;

    public function __construct(
        RaceEventRepository $raceEventRepository,
        APIResponseBuilder $apiResponseBuilder,
        FormFactoryInterface $formFactory,
        UserRepository $userRepository,
        RaceEventCompletedRepository $raceEventCompletedRepository
    ) {
        $this->raceEventRepository = $raceEventRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->formFactory = $formFactory;
        $this->userRepository = $userRepository;
        $this->raceEventCompletedRepository = $raceEventCompletedRepository;
    }

    /**
     * @param string $id
     * @param string $userId
     * @param array  $parameters
     *
     * @return \AppBundle\Model\APIResponse
     */
    public function handleCreateOrUpdate(string $id, string $userId, array $parameters)
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

        $statusCode = 204;
        $method = 'PUT';
        $raceEventCompleted = $this->raceEventCompletedRepository->findByRaceEventAndUser($raceEvent, $user);
        if ($raceEventCompleted === null) {
            $raceEventCompleted = new RaceEventCompleted();
            $raceEventCompleted->setRaceEvent($raceEvent);
            $raceEventCompleted->setUser($user);
            $statusCode = 201;
            $method = 'POST';
        }

        /** @var Form $form */
        $form = $this->formFactory->create('AppBundle\Form\Type\RaceEventCompletedType', $raceEventCompleted, ['method' => $method]);
        $clearMissing = ($method !== 'PUT') ? true : false;
        $form->submit($parameters, $clearMissing);
        if (!$form->isValid()) {
            return $this->apiResponseBuilder->buildFormErrorResponse($form);
        }

        $raceEventCompleted = $form->getData();
        $this->raceEventCompletedRepository->add($raceEventCompleted);
        $this->raceEventCompletedRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse($statusCode);
    }

    /**
     * @param string $id
     * @param string $userId
     *
     * @return \AppBundle\Model\APIResponse
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

        $raceEventCompleted = $this->raceEventCompletedRepository->findByRaceEventAndUser($raceEvent, $user);
        if ($raceEventCompleted === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('RaceEventCompleted Data not found');
        }

        $this->raceEventCompletedRepository->remove($raceEventCompleted);
        $this->raceEventCompletedRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse(204);
    }
}
