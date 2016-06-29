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
 * SearchHandler handler.
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
}
