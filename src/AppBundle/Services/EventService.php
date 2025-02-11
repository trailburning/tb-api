<?php

namespace AppBundle\Services;

use AppBundle\Entity\Event;
use AppBundle\Form\Type\EventType;
use AppBundle\Repository\EventCustomRepository;
use AppBundle\Repository\EventRepository;
use AppBundle\Repository\JourneyRepository;
use AppBundle\Model\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactoryInterface;

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
     * @var EventCustomRepository
     */
    protected $eventCustomRepository;

    /**
     * @param EventRepository       $eventRepository
     * @param JourneyRepository     $journeyRepository
     * @param APIResponseBuilder    $apiResponseBuilder
     * @param FormFactoryInterface  $formFactory
     * @param Router                $router
     * @param EventCustomRepository $eventCustomRepository
     */
    public function __construct(
        EventRepository $eventRepository,
        JourneyRepository $journeyRepository,
        APIResponseBuilder $apiResponseBuilder,
        FormFactoryInterface $formFactory,
        Router $router,
        EventCustomRepository $eventCustomRepository
    ) {
        $this->eventRepository = $eventRepository;
        $this->journeyRepository = $journeyRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->eventCustomRepository = $eventCustomRepository;
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
     * @param array  $parameters
     * @param Event  $event
     * @param string $method
     *
     * @return APIResponse
     */
    public function createOrUpdateFromAPI(array $parameters, Event $event = null, $method = 'POST')
    {
        if ($event === null) {
            $event = new Event();
        }

        $this->clearCustomFields($parameters, $event);

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
     * @param array $parameters
     * @param Event $event
     */
    private function clearCustomFields(array $parameters, Event $event)
    {
        if (array_key_exists('custom', $parameters) && count($event->getCustom()) > 0) {
            $this->eventCustomRepository->deleteByEvent($event);
        }
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
