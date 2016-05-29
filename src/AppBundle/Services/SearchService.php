<?php

namespace AppBundle\Services;

use Exception;
use DateTime;
use Elasticsearch\Client;
use ONGR\ElasticsearchDSL\Search;
use ONGR\ElasticsearchDSL\Query\BoolQuery;
use ONGR\ElasticsearchDSL\Query\MultiMatchQuery;
use ONGR\ElasticsearchDSL\Query\NestedQuery;
use ONGR\ElasticsearchDSL\Query\RangeQuery;
use ONGR\ElasticsearchDSL\Query\GeoDistanceQuery;
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
        int $distance = null
    ) {
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
            $distanceValue = (int)$distance . "m";
            $queryLocation = new GeoDistanceQuery('coords', $distanceValue, $coordsValue);
            $boolQuery->add($queryLocation, BoolQuery::FILTER);
        }

        $search = new Search();
        $search->addQuery($boolQuery);

        $params = [
            'index' => 'search',
            'type' => 'race_event',
            'body' => $search->toArray(),
        ];
        
        return $this->client->search($params);
    }

    /**
     * @param array $searchResult
     *
     * @return array
     */
    public function extractRaceEventHits(array $searchResult): array
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
    public function parseCoordsParameter($coords) 
    {
        $results = preg_match('/([\d]+\.[\d]+), ([\d]+\.[\d]+)/', $coords, $match);
        if ($results === false) {
            throw new Exception('Unable to parse GeoData Point, expected format: (LNG, LAT)');
        }
        $point = new Point($match[1], $match[2], 4326);
        
        return $point;
    }
}
