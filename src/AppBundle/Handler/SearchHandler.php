<?php

namespace AppBundle\Handler;

use AppBundle\Model\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Entity\RaceEvent;
use Symfony\Component\HttpFoundation\ParameterBag;
use AppBundle\Services\SearchService;
use \DateTime;

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
        $q = $parameters->get('q');
        
        $dateFrom = null;
        if ($parameters->has('date_from')) {
            $dateFrom = DateTime::createFromFormat('Y-m-d', $parameters->get('date_from'));
        }
        
        $dateTo = null;
        if ($parameters->has('date_to')) {
            $dateTo = DateTime::createFromFormat('Y-m-d', $parameters->get('date_to'));
        }
        
        $results = $this->searchService->search($q, $dateFrom, $dateTo);
        $raceEvents = $this->searchService->extractRaceEventHits($results);

        return $this->apiResponseBuilder->buildSuccessResponse($raceEvents, 'raceevents');
    }
}
