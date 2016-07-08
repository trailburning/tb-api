<?php

namespace AppBundle\Services;

use Elasticsearch\Client;
use AppBundle\Entity\RaceEvent;

/**
 * Class SearchIndexService.
 */
class SearchIndexService
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

    /**
     * @param RaceEvent $raceEvent
     *
     * @return array
     */
    public function createRaceEvent(RaceEvent $raceEvent)
    {
        $params = [
            'body' => $this->generateRaceEventDoc($raceEvent),
            'index' => $this->indexName,
            'type' => 'race_event',
            'id' => $raceEvent->getOid(),
        ];
        
        return $this->client->index($params);
    }

    /**
     * @param RaceEvent $raceEvent
     *
     * @return array
     */
    public function updateRaceEvent(RaceEvent $raceEvent)
    {
        $params = [
            'body' => [
                'doc' => $this->generateRaceEventDoc($raceEvent),
            ],
            'index' => $this->indexName,
            'type' => 'race_event',
            'id' => $raceEvent->getOid(),
        ];
        
        return $this->client->update($params);
    }

    /**
     * @param RaceEvent $raceEvent
     *
     * @return array
     */
    public function deleteRaceEvent(RaceEvent $raceEvent)
    {
        $params = [
            'index' => $this->indexName,
            'type' => 'race_event',
            'id' => $raceEvent->getOid(),
        ];

        return $this->client->delete($params);
    }

    /**
     * @param RaceEvent $raceEvent
     *
     * @return array
     */
    private function generateRaceEventDoc(RaceEvent $raceEvent)
    {
        $doc = [
            'id' => $raceEvent->getOid(),
            'name' => $raceEvent->getName(),
            'about' => $raceEvent->getAbout(),
            'website' => $raceEvent->getWebsite(),
            'coords' => $raceEvent->getCoordsAsArray(),
            'location' => $raceEvent->getLocation(),
            'races' => [],
            'type' => [],
            'category' => [],
        ];
        
        if ($raceEvent->getStartDate() !== null) {
            $doc['date'] = $raceEvent->getStartDate()->format('Y-m-d');
        }

        $earliestDate = null;
        foreach ($raceEvent->getRaces() as $race) {
            $doc['races'][] = [
                'id' => $race->getOid(),
                'name' => $race->getName(),
                'date' => $race->getDateAsString(),
                'type' => $race->getType(),
                'category' => $race->getCategory(),
                'distance' => $race->getDistance(),
            ];
            if ($race->getType() !== null) {
                $doc['type'][] = $race->getType();
            }
            if ($race->getCategory() !== null) {
                $doc['category'][] = $race->getCategory();
            }
        }

        if (count($doc['type']) > 1) {
            $doc['type'] = array_values(array_unique($doc['type']));
        }
        if (count($doc['category']) > 1) {
            $doc['category'] = array_values(array_unique($doc['category']));
        }

        return $doc;
    }
}
