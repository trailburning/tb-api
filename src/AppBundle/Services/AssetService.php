<?php

namespace AppBundle\Services;

use AppBundle\Response\APIResponse;
use AppBundle\Repository\AssetRepository;
use AppBundle\Repository\EventRepository;
use AppBundle\Response\APIResponseBuilder;

/**
 * Class AssetService.
 */
class AssetService
{
    /**
     * @var AssetRepository
     */
    protected $assetRepository;
    
    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * @var APIResponseBuilder
     */
    protected $apiResponseBuilder;

    /**
     * @param AssetRepository    $assetRepository
     * @param EventRepository    $eventRepository
     * @param APIResponseBuilder $apiResponseBuilder
     */
    public function __construct(AssetRepository $assetRepository, EventRepository $eventRepository, APIResponseBuilder $apiResponseBuilder)
    {
        $this->assetRepository = $assetRepository;
        $this->eventRepository = $eventRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
    }

    /**
     * @param int $eventId
     *
     * @return APIResponse
     */
    public function buildGetByEventAPIResponse($eventId)
    {
        $event = $this->eventRepository->findOneBy([
            'id' => $eventId,
        ]);

        if ($event === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Event not found.');
        }

        $assets = $this->assetRepository->findBy([
            'event' => $event,
        ]);

        return $this->apiResponseBuilder->buildSuccessResponse($assets, 'assets');
    }
}
