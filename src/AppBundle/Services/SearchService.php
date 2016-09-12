<?php

namespace AppBundle\Services;

use Elasticsearch\Client;
use ONGR\ElasticsearchDSL\Search as SearchQuery;
use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\Query\MatchQuery;
use ONGR\ElasticsearchDSL\Query\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\NestedQuery;
use ONGR\ElasticsearchDSL\Query\RangeQuery;
use ONGR\ElasticsearchDSL\Query\GeoDistanceQuery;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use AppBundle\Model\Search;
use AppBundle\DBAL\Types\SearchOrder;
use AppBundle\DBAL\Types\SearchSort;

/**
 * Class SearchService.
 */
class SearchService
{
    /**
     * @var Client
     */
    private $client;
    
    /**
     * @var string
     */
    private $indexName;

    /**
     * @param Client $client
     * @param string $indexName
     */
    public function __construct(Client $client, string $indexName)
    {
        $this->client = $client;
        $this->indexName = $indexName;
    }

    public function search(Search $search)
    {
        $searchQuery = new SearchQuery();

        $searchQuery->setFrom($search->getOffset());
        $searchQuery->setSize($search->getLimit());
        $boolQuery = new BoolQuery();
        $boolQuery->addParameter('minimum_should_match', 1);

        $this->handleSearchParameterQ($boolQuery, $search);
        $this->handleSearchParameterDateFromTo($boolQuery, $search);
        $this->handleSearchParameterDistanceFromTo($boolQuery, $search);
        $this->handleSearchParameterCoords($boolQuery, $search);
        $this->handleSearchParameterType($boolQuery, $search);
        $this->handleSearchParameterCategory($boolQuery, $search);
        $this->handleSearchSort($searchQuery, $search);

        $searchQuery->addQuery($boolQuery);

        $params = [
            'index' => $this->indexName,
            'type' => 'race_event',
            'body' => $searchQuery->toArray(),
        ];

        return $this->client->search($params);
    }
    
    private function handleSearchParameterQ(BoolQuery $boolQuery, Search $search) : BoolQuery
    {
        $parameters = [
            'operator' => 'and',
        ];
        if ($search->getQ() !== null) {
            $queryTerm = new MultiMatchQuery([
                'name',
                'about',
                'type',
                'category',
                'location',
            ], $search->getQ(), $parameters);
            $boolQuery->add($queryTerm, BoolQuery::SHOULD);

            $queryRace = new MultiMatchQuery([
                'races.name',
            ], $search->getQ(), $parameters);
            $nestedRace = new NestedQuery('races', $queryRace);
            $boolQuery->add($nestedRace, BoolQuery::SHOULD);
        }
        
        return $boolQuery;
    }
    
    private function handleSearchParameterDateFromTo(BoolQuery $boolQuery, Search $search) : BoolQuery
    {
        if ($search->getDateFrom() !== null || $search->getDateTo() !== null) {
            $dateRange = [];
            if ($search->getDateFrom() !== null) {
                $dateRange[RangeQuery::GTE] = $search->getDateFrom()->format('Y-m-d');
            }
            if ($search->getDateTo() !== null) {
                $dateRange[RangeQuery::LTE] = $search->getDateTo()->format('Y-m-d');
            }
            $queryDate = new RangeQuery('races.date', $dateRange);
            $nestedDate = new NestedQuery('races', $queryDate);
            $boolQuery->add($nestedDate, BoolQuery::FILTER);
        }
        
        return $boolQuery;
    }
    
    private function handleSearchParameterDistanceFromTo(BoolQuery $boolQuery, Search $search) : BoolQuery
    {
        if ($search->getDistanceFrom() !== null || $search->getDistanceTo() !== null) {
            $distanceRange = [];
            if ($search->getDistanceFrom() !== null) {
                $distanceRange[RangeQuery::GTE] = $search->getDistanceFrom();
            }
            if ($search->getDistanceTo() !== null) {
                $distanceRange[RangeQuery::LTE] = $search->getDistanceTo();
            }
            $queryDistance = new RangeQuery('races.distance', $distanceRange);
            $nestedDistance = new NestedQuery('races', $queryDistance);
            $boolQuery->add($nestedDistance, BoolQuery::FILTER);
        }
        
        return $boolQuery;
    }
    
    private function handleSearchParameterCoords(BoolQuery $boolQuery, Search $search) : BoolQuery
    {
        if ($search->getCoords() !== null) {
            if ($search->getDistance() === null) {
                $search->setDistance(50000);
            }
            $distanceValue = $search->getDistance().'m';
            $queryLocation = new GeoDistanceQuery('coords', $distanceValue, $search->getCoordsAsAsocArray());
            $boolQuery->add($queryLocation, BoolQuery::FILTER);
        }
        
        return $boolQuery;
    }
    
    private function handleSearchParameterType(BoolQuery $boolQuery, Search $search) : BoolQuery
    {
        if ($search->getType() !== null) {
            $queryType = new MatchQuery('races.type', $search->getType());
            $nestedType = new NestedQuery('races', $queryType);
            $boolQuery->add($nestedType, BoolQuery::FILTER);
        }
    
        return $boolQuery;
    }

    private function handleSearchParameterCategory(BoolQuery $boolQuery, Search $search) : BoolQuery
    {
        if ($search->getCategory() !== null) {
            $queryCategory = new MatchQuery('races.category', $search->getCategory());
            $nestedCategory = new NestedQuery('races', $queryCategory);
            $boolQuery->add($nestedCategory, BoolQuery::FILTER);
        }
    
        return $boolQuery;
    }

    private function handleSearchSort(SearchQuery $searchQuery, Search $search) : SearchQuery
    {
        if ($this->isSortByDistance($search->getCoords(), $search->getSort())) {
            if ($search->getOrder() === null) {
                $search->setOrder(SearchOrder::ASC);
            }
            $fieldSort = new FieldSort('_geo_distance', $search->getOrder(), [
                'coords' => $search->getCoordsAsAsocArray(),
                'distance_type' => 'sloppy_arc',
            ]);
            $searchQuery->addSort($fieldSort);
        } elseif ($search->getSort() === SearchSort::DATE) {
            if ($search->getOrder() === null) {
                $search->setOrder(SearchOrder::ASC);
            }
            $fieldSort = new FieldSort('date', $search->getOrder());
            $searchQuery->addSort($fieldSort);
        } else {
            if ($search->getOrder() === null) {
                $search->setOrder(SearchOrder::DESC);
            }
            $fieldSort = new FieldSort('_score', $search->getOrder());
            $searchQuery->addSort($fieldSort);
        }
    
        return $searchQuery;
    }

    private function isSortByDistance(Point $coords = null, string $sort = null)
    {
        if ($coords !== null && ($sort === SearchSort::DISTANCE || $sort === null)) {
            return true;
        }

        return false;
    }
}
