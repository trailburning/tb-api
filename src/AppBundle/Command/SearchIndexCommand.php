<?php 

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Doctrine\ORM\Query\ResultSetMapping;

class SearchIndexCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:search:index')
            ->setDescription('Indexes all entities a for a specified type')
            ->addArgument('type', InputArgument::REQUIRED, 'The type to index')
            ->addArgument('id', InputArgument::OPTIONAL, 'Optional a single object to index')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {   
        $this->em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $this->client = $this->getContainer()->get('vendor.elasticsearch.client');
        $this->searchIndexService = $this->getContainer()->get('app.services.searchIndex');
        $type = $input->getArgument('type');
        $id = $input->getArgument('id');
        
        switch ($type) {
            case 'race_event':
                $this->indexRaceEventType($output, $id);
                break;
            case 'autosuggest_location':
                $this->indexAutosuggestLocationType($output, $id);
                $output->writeln('OK');
                break;
            case 'autosuggest_race_event':
                $this->indexAutosuggestRaceEventType($output, $id);
                $output->writeln('OK');
                break;
            case 'autosuggest':
                $this->indexAutosuggestLocationType($output, $id);
                $this->indexAutosuggestRaceEventType($output, $id);
                $output->writeln('OK');
                break;
            case 'all':
                $this->indexRaceEventType($output);
                $this->indexAutosuggestLocationType($output, $id);
                $this->indexAutosuggestRaceEventType($output, $id);
                $output->writeln('OK');
                break;
            default:
                $output->writeln(sprintf('<error>Unknown type "%s"</error>', $type));
                break;
        }
    }
    
    protected function indexRaceEventType($output, $id = null)
    {
        if ($id == null) {
            $raceEvents = $this->em->createQuery('
                    SELECT e FROM AppBundle:RaceEvent e')
                ->getResult();
        } else {
            $raceEvents = $this->em->createQuery('
                    SELECT e FROM AppBundle:RaceEvent e
                    WHERE e.id = :id')
                ->setParameter('id', $id)
                ->getResult();
        }
        
        foreach ($raceEvents as $raceEvent) {
            $this->searchIndexService->createRaceEvent($raceEvent);
        }
        
        $output->writeln(sprintf('%s document(s) were indexed for type "race_event"', count($raceEvents)));
    }
    
    protected function indexAutosuggestLocationType($output, $id = null)
    {   
        $indexName = $this->getContainer()->getParameter('autosuggest_index_name');
        
        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('id', 'id');
        $rsm->addScalarResult('name', 'name');
        $rsm->addScalarResult('bbox_radius', 'bbox_radius');
        $rsm->addScalarResult('type', 'type');
        $rsm->addScalarResult('lng', 'lng');
        $rsm->addScalarResult('lat', 'lat');
                
        if ($id == null) {
            $query = $this->em->createNativeQuery('SELECT DISTINCT ON (name, coords, type) id, name, bbox_radius, type, ST_X(coords) AS lng, ST_Y(coords) AS lat FROM api_region ORDER BY name, coords, type, id ASC', $rsm);
        } else {
            $query = $this->em->createNativeQuery('SELECT DISTINCT ON (name, coords, type) id, name, bbox_radius, type, ST_X(coords) AS lng, ST_Y(coords) AS lat FROM api_region WHERE id =  ?', $rsm);
            $query->setParameter(1, $id);
        }

        $regions = $query->getResult();
        
        foreach ($regions as $region) {
            $coords = [floatval($region['lng']), floatval($region['lat'])];
            $doc = [
                'name' => $region['name'],
                'suggest' => [
                    'input' => [
                        $this->createShingles($region['name']),
                    ],
                    'output' => $region['name'],
                    'payload' => [
                        'type' => $region['type'],
                        'id' => $region['id'],
                        'coords' => $coords,
                        'bbox_radius' => $region['bbox_radius'],
                    ],
                ],
            ];

            $params = [
                'body' => $doc,
                'index' => $indexName,
                'type' => 'location',
                'id' => $region['id'],
            ];
            $this->client->index($params);
        }
        
        $output->writeln(sprintf('%s document(s) were indexed for type "autosuggest_location"', count($regions)));
    }
    
    protected function indexAutosuggestRaceEventType($output, $id = null)
    {        
        $indexName = $this->getContainer()->getParameter('autosuggest_index_name');
        
        if ($id == null) {
            $raceEvents = $this->em->createQuery('
                    SELECT r FROM AppBundle:RaceEvent r')
                ->getResult();
        } else {
            $raceEvents = $this->em->createQuery('
                    SELECT r FROM AppBundle:Region r
                    WHERE r.id = :id')
                ->setParameter('id', $id)
                ->getResult();
        }
        
        foreach ($raceEvents as $raceEvent) {
            $doc = [
                'name' => $raceEvent->getName(),
                'suggest' => [
                    'input' => [
                        $this->createShingles($raceEvent->getName()),
                    ],
                    'output' => $raceEvent->getName(),
                    'payload' => [
                        'type' => 'race_event',
                        'id' => $raceEvent->getId() ,
                    ],
                ],
            ];

            $params = [
                'body' => $doc,
                'index' => $indexName,
                'type' => 'race_event',
                'id' => $raceEvent->getId(),
            ];
            $this->client->index($params);
        }

        $output->writeln(sprintf('%s document(s) were indexed for type "autosuggest_race_event"', count($raceEvents)));
    }

    private function createShingles($text)
    {
        $elements = explode(" ", $text);
        $count = count($elements);
        $shingleLength = 19;

        if ($count > $shingleLength) {
            $count = $count - $shingleLength  + 1;
        } else {
            $shingleLength = $count;
        }
        $shingles = [];

        for ($i = 0; $i < $count; $i++) {
            $shingle = implode(" ", array_slice($elements, $i, $shingleLength));
            $shingles[] = $shingle;
        }

        return $shingles;
    }
}