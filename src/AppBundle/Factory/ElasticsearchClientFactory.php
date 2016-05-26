<?php 

namespace AppBundle\Factory;

use Elasticsearch\ClientBuilder;
use Elasticsearch\Client;

/**
* Description
*/
class ElasticsearchClientFactory
{
    public function create(array $config): Client
    {        
        $client = ClientBuilder::fromConfig($config, true);  
        
        return $client;
    }
}