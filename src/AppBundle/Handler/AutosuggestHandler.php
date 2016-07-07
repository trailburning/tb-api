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
        $q = strtolower($q); // FIXME: elasticsearch does not use analyzer for the term query

        $body = '{
            "query": {
                "dis_max": {
                    "tie_breaker": 0.7,
                    "queries": [
                        {
                            "term": {"suggest_engram_full": "'.$q.'" }
                        },
                        {
                            "term": {"suggest_engram_part": "'.$q.'" }
                        },
                        {
                            "match" : { "text" : "'.$q.'" }
                        }
                    ]
                 } 
             },
             "highlight": {
                 "pre_tags" : ["<strong>"],
                 "post_tags" : ["</strong>"],
                 "fields": {
                    "suggest_text": {
                        "number_of_fragments" : 1
                    },
                    "suggest_engram_full": {
                        "number_of_fragments" : 1
                    },
                    "suggest_engram_part": {
                        "number_of_fragments" : 1
                    }
               }
           }
        }';

        $params = ['index' => $this->indexName, 'body' => $body];
        $results = $this->client->search($params);
        
        return $this->apiResponseBuilder->buildSuccessResponse($results, 'autosuggest');
    }
}
