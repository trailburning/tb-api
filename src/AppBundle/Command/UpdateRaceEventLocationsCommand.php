<?php


namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class UpdateRaceEventLocationsCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:db:update-race-event-location')
            ->setDescription('Updates all race event locations')
            ->addArgument('id', InputArgument::OPTIONAL, 'Optional a single object to upate')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $mapboxAPI = $this->getContainer()->get('app.services.mapbox_api');
        $regionRepository = $this->getContainer()->get('app.repository.region');
        $id = $input->getArgument('id');

        if ($id == null) {
            $raceEvents = $em->createQuery('
                    SELECT e FROM AppBundle:RaceEvent e')
                ->getResult();
        } else {
            $raceEvents = $em->createQuery('
                    SELECT e‚ FROM AppBunde:RaceEvent
                    WHERE e.id = :id')
                ->setParameter('id', $id)
                ->getResult();
        }

        foreach ($raceEvents as $raceEvent) {
            if (count($raceEvent->getRegions()) > 0) {
                continue;
            }
            
            $regionFeatures = $mapboxAPI->reverseGeocode($raceEvent->getCoords());
            $raceEvent->setLocation($mapboxAPI->getLocationNameFromFeatures($regionFeatures));

            $regions = [];
            foreach ($regionFeatures as $regionFeature) {
                $bboxRadius = $mapboxAPI->calculateBoundingBoxRadius(
                    $regionFeature->bbox[0], 
                    $regionFeature->bbox[1], 
                    $regionFeature->bbox[2], 
                    $regionFeature->bbox[3]
                );
                $region = $regionRepository->getOrCreateRegion(
                    $regionFeature->id, 
                    $regionFeature->place_name, 
                    $regionFeature->center[0], 
                    $regionFeature->center[1], 
                    $bboxRadius
                );
                $regions[] = $region;
            }
            $raceEvent->setRegions($regions);
            
            $em->persist($raceEvent);
            usleep(100000);
        }
        $em->flush();

        $output->writeln(sprintf('%s RaceEvent object(s) were updated', count($raceEvents)));
        $output->writeln('OK');
    }
}
