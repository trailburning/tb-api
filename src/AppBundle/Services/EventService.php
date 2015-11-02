<?php

namespace AppBundle\Services;

use AppBundle\Response\APIResponse;
use AppBundle\Repository\JourneyRepository;
use AppBundle\Repository\EventRepository;
use AppBundle\Response\APIResponseBuilder;
use AppBundle\Entity\Event;
use Symfony\Component\Form\FormFactoryInterface;
use AppBundle\Form\Type\EventType;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class EventService.
 */
class EventService
{
    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * @var JourneyRepository
     */
    protected $journeyRepository;

    /**
     * @var APIResponseBuilder
     */
    protected $apiResponseBuilder;
    
    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param EventRepository    $eventRepository
     * @param JourneyRepository  $journeyRepository
     * @param APIResponseBuilder $apiResponseBuilder
     * @param FormFactoryInterface $formFactory
     * @param Router               $router
     */
    public function __construct(
        EventRepository $eventRepository, 
        JourneyRepository $journeyRepository, 
        APIResponseBuilder $apiResponseBuilder, 
        FormFactoryInterface $formFactory,
        Router $router
    ) {
        $this->eventRepository = $eventRepository;
        $this->journeyRepository = $journeyRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @param string $journeyOid
     *
     * @return APIResponse
     */
    public function buildGetByJourneyAPIResponse($journeyOid)
    {
        $journey = $this->journeyRepository->findOneBy([
            'oid' => $journeyOid,
            'publish' => true,
        ]);

        if ($journey === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Journey not found.');
        }

        $qb = $this->eventRepository->findByJourneyQB($journey);
        $qb = $this->eventRepository->addOrderByPositionQB($qb);
        $events = $qb->getQuery()->getResult();

        return $this->apiResponseBuilder->buildSuccessResponse($events, 'events');
    }
    
    /**
     * @param string $oid
     *
     * @return APIResponse
     */
    public function buildGetAPIResponse($oid)
    {
        $events = $this->eventRepository->findBy([
            'oid' => $oid,
        ]);

        if (count($events) === 0) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Event not found.');
        }

        return $this->apiResponseBuilder->buildSuccessResponse($events, 'events');
    }
    
    /**
     * @param Event $event
     * @param array   $parameters
     * @param string  $method
     *
     * @return APIResponse
     */
    public function createOrUpdateFromAPI(array $parameters, Event $event = null, $method = 'POST')
    {
        if ($event === null) {
            $event = new Event();
        }

        $form = $this->formFactory->create(new EventType(), $event, ['method' => $method]);
        $clearMissing = ($method !== 'PUT') ? true : false;
        $form->submit($parameters, $clearMissing);

        if (!$form->isValid()) {
            return $this->apiResponseBuilder->buildFormErrorResponse($form);
        }

        $event = $form->getData();

        $this->eventRepository->add($event);
        $this->eventRepository->store();

        $response = $this->apiResponseBuilder->buildEmptyResponse(204);
        if ($method === 'POST') {
            $response->setStatusCode(201);
            $location = $this->router->generate('get_events', ['id' => $event->getOid()]);
            $response->addHeader('Location', $location);
        }

        return $response;
    }

    /**
     * @param string $id
     *
     * @return APIResponse
     */
    public function deleteFromAPI($id)
    {
        $event = $this->eventRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($event === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Event not found');
        }

        $this->eventRepository->remove($event);
        $this->eventRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse(204);
    }
}
