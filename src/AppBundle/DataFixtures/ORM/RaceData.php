<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Race;

/**
 * Race data fixtures.
 */
class RaceData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $race = new Race();
        $race->setName('Grand 46K');
        $race->setDate(new \DateTime('2017-05-16'));
        $race->setType('road');
        $race->setDistance('marathon');
        $race->setRaceEvent($this->getReference('RaceEvent-1'));
        $manager->persist($race);
        $this->addReference('Race-1', $race);
        
        $race = new Race();
        $race->setName('Media 30K');
        $race->setDate(new \DateTime('2017-05-17'));
        $race->setType('road');
        $race->setDistance('marathon');
        $race->setRaceEvent($this->getReference('RaceEvent-1'));
        $manager->persist($race);
        $this->addReference('Race-2', $race);
        
        $race = new Race();
        $race->setName('Berlin Marathon');
        $race->setDate(new \DateTime('2017-06-01'));
        $race->setType('road');
        $race->setDistance('marathon');
        $race->setRaceEvent($this->getReference('RaceEvent-2'));
        $manager->persist($race);
        $this->addReference('Race-3', $race);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ];
    }
}
