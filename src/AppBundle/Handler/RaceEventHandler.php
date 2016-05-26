<?php

namespace AppBundle\Handler;

use AppBundle\Model\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Repository\RaceEventRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use AppBundle\Entity\RaceEvent;

/**
 * RaceEvent handler.
 */
class RaceEventHandler
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
     * @var Router
     */
    private $router;

    /**
     * @param RaceEventRepository  $raceEventRepository
     * @param APIResponseBuilder   $apiResponseBuilder
     * @param FormFactoryInterface $formFactory
     * @param Router               $router
     */
    public function __construct(
        RaceEventRepository $raceEventRepository,
        APIResponseBuilder $apiResponseBuilder,
        FormFactoryInterface $formFactory,
        Router $router
    ) {
        $this->raceEventRepository = $raceEventRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @param string $id
     *
     * @return APIResponse
     */
    public function handleGet($id)
    {
        $raceEvents = $this->raceEventRepository->findBy([
            'oid' => $id,
        ]);

        if (count($raceEvents) === 0) {
            return $this->apiResponseBuilder->buildNotFoundResponse('RaceEvent not found.');
        }

        return $this->apiResponseBuilder->buildSuccessResponse($raceEvents, 'raceevents');
    }

    /**
     * @return APIResponse
     */
    public function handleGetList()
    {
        $raceEvents = $this->raceEventRepository->findAll();

        return $this->apiResponseBuilder->buildSuccessResponse($raceEvents, 'raceevents');
    }

    /**
     * @param array     $parameters
     * @param RaceEvent $raceEvent
     * @param string    $method
     *
     * @return APIResponse
     */
    public function handleCreateOrUpdate(array $parameters, RaceEvent $raceEvent = null, $method = 'POST')
    {
        if ($raceEvent === null) {
            $raceEvent = new RaceEvent();
        }

        $form = $this->formFactory->create('AppBundle\Form\RaceEventType', $raceEvent, ['method' => $method]);
        $clearMissing = ($method !== 'PUT') ? true : false;
        $form->submit($parameters, $clearMissing);

        if (!$form->isValid()) {
            return $this->apiResponseBuilder->buildFormErrorResponse($form);
        }

        $raceEvent = $form->getData();

        $this->raceEventRepository->add($raceEvent);
        $this->raceEventRepository->store();

        $response = $this->apiResponseBuilder->buildEmptyResponse(204);
        if ($method === 'POST') {
            $response->setStatusCode(201);
            $location = $this->router->generate('get_race_event', ['id' => $raceEvent->getOid()]);
            $response->addHeader('Location', $location);
        }

        return $response;
    }

    /**
     * @param string $id
     *
     * @return APIResponse
     */
    public function handleDelete($id)
    {
        $raceEvent = $this->raceEventRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($raceEvent === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('RaceEvent not found');
        }

        $this->raceEventRepository->remove($raceEvent);
        $this->raceEventRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse(204);
    }
}
