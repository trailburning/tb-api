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
use AppBundle\DBAL\Types\RaceType;
use AppBundle\DBAL\Types\RaceCategory;
use Symfony\Component\Form\FormFactoryInterface;
use AppBundle\Model\Search;

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
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @param APIResponseBuilder $apiResponseBuilder
     * @param SearchService      $searchService
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        APIResponseBuilder $apiResponseBuilder,
        SearchService $searchService,
        FormFactoryInterface $formFactory
    ) {
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->searchService = $searchService;
        $this->formFactory = $formFactory;
    }

    /**
     * @param array $parameters
     *
     * @return APIResponse
     */
    public function handleSearch(array $parameters)
    {
        $form = $this->formFactory->create('AppBundle\Form\Type\SearchType', new Search(), ['method' => 'GET']);
        $form->submit($parameters);

        if (!$form->isValid()) {
            return $this->apiResponseBuilder->buildFormErrorResponse($form);
        }

        $search = $form->getData();
        $results = $this->searchService->search($search);
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
        
        $validParameters = [
            'relevance',
            'distance',
        ];
        
        $sort = trim($parameters->get($key));
        if (!in_array($sort, $validParameters)) {
            throw new Exception($key . ': Invalid value. Allowed values are: "' . implode('", "', $validParameters) . '"');
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
        
        $validParameters = [
            'asc',
            'desc',
        ];
        
        $order = trim($parameters->get($key));
        if (!in_array($order, $validParameters)) {
            throw new Exception($key . ': Invalid value. Allowed values are: "' . implode('", "', $validParameters) . '"');
        }
        
        return $order;
    }
    
    /**
     * @param ParameterBag $parameters
     * @param string $key 
     * @return integer
     * @throws Exception
     */
    private function parseTypeFilterParameter(ParameterBag $parameters, string $key)
    {
        if (!$parameters->has($key)) {
            return null;
        }
        
        $validParameters = RaceType::getValues();
        
        $order = trim($parameters->get($key));
        if (!in_array($order, $validParameters)) {
            throw new Exception($key . ': Invalid value. Allowed values are: "' . implode('", "', $validParameters) . '"');
        }
        
        return $order;
    }
    
    /**
     * @param ParameterBag $parameters
     * @param string $key 
     * @return integer
     * @throws Exception
     */
    private function parseCategoryFilterParameter(ParameterBag $parameters, string $key)
    {
        if (!$parameters->has($key)) {
            return null;
        }
        
        $validParameters = RaceCategory::getValues();
        
        $order = trim($parameters->get($key));
        if (!in_array($order, $validParameters)) {
            throw new Exception($key . ': Invalid value. Allowed values are: "' . implode('", "', $validParameters) . '"');
        }
        
        return $order;
    }
}
