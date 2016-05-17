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
        $race->setName('name');
        $race->setDate(new \DateTime('2016-05-17'));
        $race->setType('road');
        $race->setDistance('marathon');
        $race->setRaceEvent($this->getReference('RaceEvent-1'));
        $manager->persist($race);
        $this->addReference('Race-1', $race);

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
