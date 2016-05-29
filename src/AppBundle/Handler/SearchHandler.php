<?php

namespace AppBundle\Handler;

use Exception;
use DateTime;
use AppBundle\Model\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Entity\RaceEvent;
use Symfony\Component\HttpFoundation\ParameterBag;
use AppBundle\Services\SearchService;
use CrEOF\Spatial\PHP\Types\Geometry\Point;

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
            if ($dateFrom === false) {
                return $this->apiResponseBuilder->buildBadRequestResponse('Unable to parse date_from, expected format: yyyy-MM-dd');
            }
        }

        $dateTo = null;
        if ($parameters->has('date_to')) {
            $dateTo = DateTime::createFromFormat('Y-m-d', $parameters->get('date_to'));
            if ($dateTo === false) {
                return $this->apiResponseBuilder->buildBadRequestResponse('Unable to parse date_to, expected format: yyyy-MM-dd');
            }
        }

        $coords = null;
        $distance = null;
        if ($parameters->has('coords')) {
            try {
                $coords = $this->parseCoordsParameter($parameters->get('coords'));
                $distance = $this->parseDistanceParameter($parameters->get('distance', 50000));
            } catch (Exception $e) {
                return $this->apiResponseBuilder->buildBadRequestResponse($e->getMessage());
            }
        }

        $results = $this->searchService->search($q, $dateFrom, $dateTo, $coords, $distance);
        $raceEvents = $this->extractRaceEventHits($results);

        return $this->apiResponseBuilder->buildSuccessResponse($raceEvents, 'raceevents');
    }
    
    /**
     * @param array $searchResult
     *
     * @return array
     */
    private function extractRaceEventHits(array $searchResult): array
    {
        $filter = [
            'type', 
            'category',
        ];
        $results = [];
        if (isset($searchResult['hits']['hits'])) {
            foreach ($searchResult['hits']['hits'] as $result) {
                $raceEvent = array_diff_key($result['_source'], array_flip($filter));
                $results[] = $raceEvent;
            }
        }

        return $results;
    }
    
    /**
     * @param string $coords 
     * @return Point
     * @throws Exception
     */
    private function parseCoordsParameter($value) 
    {
        $results = preg_match('/^\(([\d]+\.[\d]+),\s?([\d]+\.[\d]+)\)$/', trim($value), $match);
        if ($results !== 1) {
            throw new Exception('Unable to parse GeoData Point, expected format: (LNG, LAT)');
        }
        
        $point = new Point($match[1], $match[2], 4326);
        
        return $point;
    }
    
    private function parseDistanceParameter($value): int 
    {
        $distance = intval($value);
        if (!$distance > 0) {
            throw new Exception('Distance must be a positive integer value.');
        }
        
        return $distance;
    }
}
