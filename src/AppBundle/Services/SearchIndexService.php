<?php

namespace AppBundle\Services;

use Elasticsearch\Client;
use AppBundle\Entity\RaceEvent;
use Elasticsearch\Common\Exceptions\Missing404Exception;

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
    private $searchIndexName;

    /**
     * @var string
     */
    private $autosuggestIndexName;

    /**
     * @param Client $client
     * @param string $searchIndexName
     */
    public function __construct(Client $client, string $searchIndexName, string $autosuggestIndexName)
    {
        $this->client = $client;
        $this->searchIndexName = $searchIndexName;
        $this->autosuggestIndexName = $autosuggestIndexName;
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
            'index' => $this->searchIndexName,
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
            'index' => $this->searchIndexName,
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
            'index' => $this->searchIndexName,
            'type' => 'race_event',
            'id' => $raceEvent->getOid(),
        ];

        try {
            return $this->client->delete($params);
        } catch (Missing404Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param RaceEvent $raceEvent
     *
     * @return array|false
     */
    public function deleteRaceEventAutosuggest(RaceEvent $raceEvent)
    {
        $params = [
            'index' => $this->autosuggestIndexName,
            'type' => 'race_event',
            'body' => [
                'query' => [
                    'term' => [
                        'oid' => $raceEvent->getOid(),
                    ],
                ],
            ],
        ];

        $result = $this->client->search($params);
        if (count($result['hits']['hits']) === 0) {
            return;
        }

        $id = $result['hits']['hits'][0]['_id'];

        $params = [
            'index' => $this->autosuggestIndexName,
            'type' => 'race_event',
            'id' => $id,
        ];

        try {
            $result = $this->client->delete($params);
        } catch (Missing404Exception $e) {
            $result = $e->getMessage();
        }

        return $result;
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
            'attributes' => $raceEvent->getAttributesArray(),
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
