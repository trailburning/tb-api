<?php

namespace AppBundle\Services;

use Elasticsearch\Client;

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

    /**
     * @param string $q
     */
    public function search(string $q)
    {
        $params = [
            'index' => 'search',
            'type' => 'race_event',
            'body' => [],
        ];

        if ($q !== '') {
            $params['body']['query'] = [
                'bool' => [
                    'should' => [
                        [
                            'multi_match' => [
                                'query' => $q,
                                'fields' => ['name', 'about'],
                            ]
                        ],
                        [
                            'nested' => [
                                'path' => 'races',
                                'query' => [
                                    'multi_match' => [
                                        'query' => $q,
                                        'fields' => ['races.name'],
                                    ],
                                ],
                            ],
                        ],
                    ],
                ],
            ];
        }

        return $this->client->search($params);
    }

    /**
     * @param array $searchResult
     *
     * @return array
     */
    public function extractRaceEventHits(array $searchResult)
    {
        $results = [];
        if (isset($searchResult['hits']['hits'])) {
            foreach ($searchResult['hits']['hits'] as $result) {
                $results[] = $result['_source'];
            }
        }

        return $results;
    }
}
