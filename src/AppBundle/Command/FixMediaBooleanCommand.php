<?php


namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class FixMediaBooleanCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('app:search:fix-media')
            ->setDescription('Fixes the media published value')
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $searchindex = $this->getContainer()->get('app.services.searchindex');

        $raceEvents = $em->createQuery('
                SELECT e, m FROM AppBundle:RaceEvent e
                INNER JOIN e.medias m')
            ->getResult();

        foreach ($raceEvents as $raceEvent) {
            $searchindex->updateRaceEvent($raceEvent);
        }

    }
}
