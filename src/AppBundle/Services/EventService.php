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
}
