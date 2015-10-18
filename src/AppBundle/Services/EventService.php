<?php

namespace AppBundle\Services;

use AppBundle\Response\APIResponse;
use AppBundle\Repository\JourneyRepository;
use AppBundle\Repository\EventRepository;
use AppBundle\Response\APIResponseBuilder;

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
     * @param EventRepository    $eventRepository
     * @param JourneyRepository  $journeyRepository
     * @param APIResponseBuilder $apiResponseBuilder
     */
    public function __construct(EventRepository $eventRepository, JourneyRepository $journeyRepository, APIResponseBuilder $apiResponseBuilder)
    {
        $this->eventRepository = $eventRepository;
        $this->journeyRepository = $journeyRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
    }

    /**
     * @param int $journeyId
     *
     * @return APIResponse
     */
    public function buildGetByJourneyAPIResponse($journeyId)
    {
        $journey = $this->journeyRepository->findOneBy([
            'id' => $journeyId,
            'publish' => true,
        ]);

        if ($journey === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Journey not found.');
        }

        $events = $this->eventRepository->findBy([
            'journey' => $journey,
        ]);

        return $this->apiResponseBuilder->buildSuccessResponse($events, 'events');
    }
}
