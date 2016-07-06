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
        $searchQuery->setFrom(0);
        $searchQuery->setSize(10000);
        $boolQuery = new BoolQuery();
        $boolQuery->addParameter('minimum_should_match', 1);

        if ($search->getQ() !== null) {
            $queryTerm = new MultiMatchQuery([
                'name',
                'about',
                'type',
                'category',
                'location',
            ], $search->getQ());
            $boolQuery->add($queryTerm, BoolQuery::SHOULD);

            $queryRace = new MultiMatchQuery([
                'races.name',
            ], $search->getQ());
            $nestedRace = new NestedQuery('races', $queryRace);
            $boolQuery->add($nestedRace, BoolQuery::SHOULD);
        }

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

        if ($search->getCoords() !== null) {
            $coordsValue = [
                'lat' => $search->getCoords()->getLatitude(),
                'lon' => $search->getCoords()->getLongitude(),
            ];
            if ($search->getDistance() === null) {
                $search->setDistance(50000);
            }
            $distanceValue = $search->getDistance().'m';
            $queryLocation = new GeoDistanceQuery('coords', $distanceValue, $coordsValue);
            $boolQuery->add($queryLocation, BoolQuery::FILTER);
        }

        if ($search->getType() !== null) {
            $queryType = new MatchQuery('races.type', $search->getType());
            $nestedType = new NestedQuery('races', $queryType);
            $boolQuery->add($nestedType, BoolQuery::FILTER);
        }

        if ($search->getCategory() !== null) {
            $queryCategory = new MatchQuery('races.category', $search->getCategory());
            $nestedCategory = new NestedQuery('races', $queryCategory);
            $boolQuery->add($nestedCategory, BoolQuery::FILTER);
        }

        if ($this->sortByDistance($search->getCoords(), $search->getSort())) {
            if ($search->getOrder() === null) {
                $search->setOrder(SearchOrder::ASC);
            }
            $fieldSort = new FieldSort('_geo_distance', $search->getOrder(), [
                'coords' => $coordsValue,
                'distance_type' => 'sloppy_arc',
            ]);
            $searchQuery->addSort($fieldSort);
        } else {
            if ($search->getOrder() === null) {
                $search->setOrder(SearchOrder::DESC);
            }
            $fieldSort = new FieldSort('_score', $search->getOrder());
            $searchQuery->addSort($fieldSort);
        }

        $searchQuery->addQuery($boolQuery);

        $params = [
            'index' => $this->indexName,
            'type' => 'race_event',
            'body' => $searchQuery->toArray(),
        ];

        return $this->client->search($params);
    }

    private function sortByDistance(Point $coords = null, string $sort = null)
    {
        if ($coords !== null && ($sort === 'distance' || $sort === null)) {
            return true;
        }

        return false;
    }
}
