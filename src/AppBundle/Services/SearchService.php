<?php

namespace AppBundle\Services;

use DateTime;
use Elasticsearch\Client;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\Query\MatchQuery;
use ONGR\ElasticsearchDSL\Query\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\NestedQuery;
use ONGR\ElasticsearchDSL\Query\RangeQuery;
use ONGR\ElasticsearchDSL\Query\GeoDistanceQuery;
use ONGR\ElasticsearchDSL\Sort\FieldSort;
use CrEOF\Spatial\PHP\Types\Geometry\Point;

/**
 * Class MediaService.
 */
class SearchService
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @param Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function search(
        string $term = null,
        DateTime $dateFrom = null,
        DateTime $dateTo = null,
        Point $coords = null,
        int $distance = null,
        string $sort = null,
        string $order = null,
        string $type = null,
        string $category = null
    ) {
        $search = new Search();
        $search->setFrom(0);
        $search->setSize(100);
        $boolQuery = new BoolQuery();
        $boolQuery->addParameter('minimum_should_match', 1);

        if ($term !== null) {
            $queryTerm = new MultiMatchQuery([
                'name',
                'about',
                'type',
                'category',
                'location',
            ], $term);
            $boolQuery->add($queryTerm, BoolQuery::SHOULD);

            $queryRace = new MultiMatchQuery([
                'races.name',
            ], $term);
            $nestedRace = new NestedQuery('races', $queryRace);
            $boolQuery->add($nestedRace, BoolQuery::SHOULD);
        }

        if ($dateFrom !== null || $dateTo !== null) {
            $dateRange = [];
            if ($dateFrom !== null) {
                $dateRange[RangeQuery::GTE] = $dateFrom->format('Y-m-d');
            }
            if ($dateTo !== null) {
                $dateRange[RangeQuery::LTE] = $dateTo->format('Y-m-d');
            }
            $queryDate = new RangeQuery('races.date', $dateRange);
            $nestedDate = new NestedQuery('races', $queryDate);
            $boolQuery->add($nestedDate, BoolQuery::FILTER);
        }

        if ($coords !== null) {
            $coordsValue = [
                'lat' => $coords->getLatitude(),
                'lon' => $coords->getLongitude(),
            ];
            $distanceValue = (int) $distance.'m';
            $queryLocation = new GeoDistanceQuery('coords', $distanceValue, $coordsValue);
            $boolQuery->add($queryLocation, BoolQuery::FILTER);
        }
        
        if ($type !== null) {
            $queryType = new MatchQuery('races.type', $type);
            $nestedType = new NestedQuery('races', $queryType);
            $boolQuery->add($nestedType, BoolQuery::FILTER);
        }
        
        if ($category !== null) {
            $queryCategory = new MatchQuery('races.category', $category);
            $nestedCategory = new NestedQuery('races', $queryCategory);
            $boolQuery->add($nestedCategory, BoolQuery::FILTER);
        }
        
        if ($this->sortByDistance($coords, $sort)) {
            if ($order === null) {
                $order == 'asc';
            }
            $fieldSort = new FieldSort('_geo_distance', $order, [
                'coords' => $coordsValue,
                'distance_type' => 'sloppy_arc',
            ]);
            $search->addSort($fieldSort);
        } else {
            if ($order === null) {
                $order == 'desc';
            }
            $fieldSort = new FieldSort('_score', $order);
            $search->addSort($fieldSort);
        }

        $search->addQuery($boolQuery);

        $params = [
            'index' => 'search',
            'type' => 'race_event',
            'body' => $search->toArray(),
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
