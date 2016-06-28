<?php

namespace AppBundle\Services;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use GuzzleHttp\Client;

/**
 * Class MapboxAPI.
 */
class MapboxAPI
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $accessToken;

    /**
     * @param Client $client
     * @param string $accessToken
     */
    public function __construct(Client $client, string $accessToken)
    {
        $this->client = $client;
        $this->accessToken = $accessToken;
    }

    /**
     * @param Point $point 
     * @return string
     */
    public function reverseGeocode(Point $point) : string
    {
        $url = sprintf('/geocoding/v5/mapbox.places/%s,%s.json?access_token=%s', 
            $point->getLongitude(),
            $point->getLatitude(), 
            $this->accessToken
        );

        $response = $this->client->request('GET', $url);
        if ($response->getStatusCode() !== 200) {
            throw new \Exception(sprintf("Unable to reverse geocode, got status code %s"), $response->getStatusCode());
        }

        $body = (string) $response->getBody();
        $response = json_decode($body);
        $region = $this->parseRegionInResponse($response);
        
        return $region;
    }
    
    /**
     * @param object $response 
     * @return string
     */
    private function parseRegionInResponse($response) : string
    {
        $features = $this->getFeaturesFromResponse($response);
        
        $region = '';
        if (isset($features['region'])) {
            $region = $features['region']->place_name;
        } elseif (isset($features['place'])) {
            $region = $features['place']->place_name;
        } elseif (count($features) > 0) {
            $region = array_pop($features)->place_name;
        }
        
        return $region;
    }
    
    /**
     * @param object $response 
     * @return array
     */
    private function getFeaturesFromResponse($response) : array
    {
        $features = [];
        
        foreach ($response->features as $feature) {
            $type = substr($feature->id, 0, strpos($feature->id, '.'));
            $features[$type] = $feature;
        }
        
        return $features;
    }
}
