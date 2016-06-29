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
     * @return object|null
     */
    public function reverseGeocode(Point $point)
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
        $responseData = json_decode($body);
        
        $feature = $this->parseFeatureInResponse($responseData);
        
        return $feature;
    }
    
    /**
     * @param object $response 
     * @return object|null
     */
    private function parseFeatureInResponse($response)
    {
        $features = $this->getFeaturesFromResponse($response);
        
        if (count($features) === 0) {
            return;
        }
        
        if (isset($features['place'])) {
            $feature = $features['place'];
        } elseif (isset($features['region'])) {
            $feature = $features['region'];
        } elseif (count($features) > 0) {
            $feature = array_pop($features);
        }
        
        return $feature;
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
