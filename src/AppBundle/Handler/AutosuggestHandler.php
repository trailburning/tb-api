<?php

namespace AppBundle\Handler;

use AppBundle\Model\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Model\Search;
use Elasticsearch\Client;

/**
 * AutosuggestHandler handler.
 */
class AutosuggestHandler
{
    /**
     * @var APIResponseBuilder
     */
    private $apiResponseBuilder;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $indexName;

    /**
     * @param APIResponseBuilder $apiResponseBuilder
     * @param Client             $client
     * @param string             $indexName
     */
    public function __construct(
        APIResponseBuilder $apiResponseBuilder,
        Client $client,
        string $indexName
    ) {
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->client = $client;
        $this->indexName = $indexName;
    }

    /**
     * @param array $parameters
     *
     * @return APIResponse
     */
    public function handleGet(array $parameters)
    {
        $q = isset($parameters['q']) ? $parameters['q'] : '';

        $params = [
            'index' => $this->indexName,
            'body' => [
                'suggest' => [
                    'text' => $q,
                    'completion' => [ 'field' => 'suggest' ]
                ],
            ],
        ];
        $results = $this->client->suggest($params);
        
        return $this->apiResponseBuilder->buildSuccessResponse($results, 'autosuggest');
    }
}
