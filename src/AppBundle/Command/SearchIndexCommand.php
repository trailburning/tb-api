<?php 

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

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
            case 'all':
                $this->indexRaceEventType($output);
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
                    SELECT e‚ FROM AppBunde:RaceEvent
                    WHERE e.id = :id')
                ->setParameter('id', $id)
                ->getResult();
        }
        
        foreach ($raceEvents as $raceEvent) {
            $this->searchIndexService->createRaceEvent($raceEvent);
        }
        
        $output->writeln(sprintf('%s document(s) were indexed for type  "race_event"', count($raceEvents)));
        $output->writeln('OK');
    }
}