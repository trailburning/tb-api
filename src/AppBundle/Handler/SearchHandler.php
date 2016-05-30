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

        $errors = [];
        $dateFrom = null;
        $dateTo = null;
        $coords = null;
        $distance = null;
        $sort = null;
        $order = null;
        
        try {
            $dateFrom = $this->parseDateParameter($parameters, 'date_from');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        
        try {
            $dateTo = $this->parseDateParameter($parameters, 'date_to');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        
        try {
            $coords = $this->parseCoordsParameter($parameters, 'coords');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        
        if ($coords !== null) {
            try {
                $distance = $this->parseDistanceParameter($parameters, 'distance');
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }
        
        try {
            $sort = $this->parseSortParameter($parameters, 'sort');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }
        
        try {
            $order = $this->parseSortOrderParameter($parameters, 'order');
        } catch (Exception $e) {
            $errors[] = $e->getMessage();
        }

        if (count($errors) > 0) {
            return $this->apiResponseBuilder->buildBadRequestResponse($errors);
        }
        
        $results = $this->searchService->search($q, $dateFrom, $dateTo, $coords, $distance, $sort, $order);
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
     * @param ParameterBag $parameters
     * @param string $key 
     * @return Point
     * @throws Exception
     */
    private function parseDateParameter(ParameterBag $parameters, string $key) 
    {
        if (!$parameters->has($key)) {
            return null;
        }
        
        $date = DateTime::createFromFormat('Y-m-d', trim($parameters->get($key)));
        if ($date === false) {
            throw new Exception($key . ': Unable to parse date, expected format: yyyy-MM-dd');
        }
        
        return $date;
    }
    
    /**
     * @param ParameterBag $parameters
     * @param string $key 
     * @return Point
     * @throws Exception
     */
    private function parseCoordsParameter(ParameterBag $parameters, string $key) 
    {
        if (!$parameters->has($key)) {
            return null;
        }
        
        $results = preg_match('/^\(([\d]+\.[\d]+),\s?([\d]+\.[\d]+)\)$/', trim($parameters->get($key)), $match);
        if ($results !== 1) {
            throw new Exception($key . ': Unable to parse GeoData Point, expected format: (LNG, LAT)');
        }
        
        $point = new Point($match[1], $match[2], 4326);
        
        return $point;
    }
    
    /**
     * @param ParameterBag $parameters
     * @param string $key 
     * @return integer
     * @throws Exception
     */
    private function parseDistanceParameter(ParameterBag $parameters, string $key)
    {
        if (!$parameters->has($key)) {
            return 5000;
        }
        
        $distance = intval(trim($parameters->get($key)));
        if (!$distance > 0) {
            throw new Exception($key . ': Value must be a positive integer.');
        }
        
        return $distance;
    }
    
    /**
     * @param ParameterBag $parameters
     * @param string $key 
     * @return integer
     * @throws Exception
     */
    private function parseSortParameter(ParameterBag $parameters, string $key)
    {
        if (!$parameters->has($key)) {
            return null;
        }
        
        $validSortParameters = [
            'relevance',
            'distance',
        ];
        
        $sort = trim($parameters->get($key));
        if (!in_array($sort, $validSortParameters)) {
            throw new Exception($key . ': Invalid value. Allowed values are: "' . implode('", "', $validSortParameters) . '"');
        }
        
        return $sort;
    }
    
    /**
     * @param ParameterBag $parameters
     * @param string $key 
     * @return integer
     * @throws Exception
     */
    private function parseSortOrderParameter(ParameterBag $parameters, string $key)
    {
        if (!$parameters->has($key)) {
            return null;
        }
        
        $validSortParameters = [
            'asc',
            'desc',
        ];
        
        $order = trim($parameters->get($key));
        if (!in_array($order, $validSortParameters)) {
            throw new Exception($key . ': Invalid value. Allowed values are: "' . implode('", "', $validSortParameters) . '"');
        }
        
        return $order;
    }
}
