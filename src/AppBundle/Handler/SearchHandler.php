<?php

namespace AppBundle\Handler;

use AppBundle\Model\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Entity\RaceEvent;
use Symfony\Component\HttpFoundation\ParameterBag;
use AppBundle\Services\SearchService;

/**
 * RaceEvent handler.
 */
class SearchHandler
{
    /**
     * @var APIResponseBuilder
     */
    private $apiResponseBuilder;

    /**
     * @var SearchService
     */
    private $searchService;

    /**
     * @param APIResponseBuilder $apiResponseBuilder
     * @param SearchService      $searchService
     */
    public function __construct(
        APIResponseBuilder $apiResponseBuilder,
        SearchService $searchService
    ) {
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->searchService = $searchService;
    }

    /**
     * @param ParameterBag $parameters
     *
     * @return APIResponse
     */
    public function handleSearch(ParameterBag $parameters)
    {   
        $results = $this->searchService->search(
            $parameters->get('q', '')
        );
        $raceEvents = $this->searchService->extractRaceEventHits($results);

        return $this->apiResponseBuilder->buildSuccessResponse($raceEvents, 'raceevents');
    }
}
