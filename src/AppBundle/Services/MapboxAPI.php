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
    public function reverseGeocode(Point $point) : array
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
        
        $features = $this->getFeaturesFromResponse($responseData);
        
        return $features;
    }
    
    /**
     * @param object $response 
     * @return string
     */
    public function getLocationNameFromFeatures(array $features) : string
    {
        $locationName = '';
        
        if (isset($features['place'])) {
            $locationName = $features['place']->place_name;
        } elseif (isset($features['region'])) {
            $locationName = $features['region']->place_name;
        } elseif (count($features) > 0) {
            $locationName = array_pop($features)->place_name;
        }
        
        return $locationName;
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
            if (in_array($type, ['country', 'region', 'place'])) {
                $features[$type] = $feature;
            }
        }
        
        return $features;
    }
    
    /**
     * @param Point $pointA 
     * @param Point $pointB 
     * @return int
     */
    public function calculateBoundingBoxRadius(float $pointALongitude, float $pointALatitude, float $pointBLongitude, float $pointBLatitude) : int
    {      
        $pointA = new Point($pointALongitude, $pointALatitude, 4326);
        $pointB = new Point($pointBLongitude, $pointBLatitude, 4326);
        
        $distance = $this->calculateDistance($pointA, $pointB);
        $radius = round($distance / 2);
        
        return $radius;
    }
    
    /**
     * @param Point $pointA 
     * @param Point $pointB 
     * @return float
     */
    public function calculateDistance(Point $pointA, Point $pointB) : float
    {      
        $earthRadius = 6371000;
        $latFrom = deg2rad($pointA->getLatitude());
        $lonFrom = deg2rad($pointA->getLongitude());
        $latTo = deg2rad($pointB->getLatitude());
        $lonTo = deg2rad($pointB->getLongitude());

        $lonDelta = $lonTo - $lonFrom;
        $a = pow(cos($latTo) * sin($lonDelta), 2) +
        pow(cos($latFrom) * sin($latTo) - sin($latFrom) * cos($latTo) * cos($lonDelta), 2);
        $b = sin($latFrom) * sin($latTo) + cos($latFrom) * cos($latTo) * cos($lonDelta);
        
        $angle = atan2(sqrt($a), $b);
        $distance = $angle * $earthRadius;
        
        return $distance;
    }
}
