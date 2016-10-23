<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\RaceEvent;
use AppBundle\Entity\RaceEventAttribute;

/**
 * RaceEvent data fixtures.
 */
class RaceEventAttributeData extends AbstractFixture implements FixtureInterface//, DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $raceEventAttribute = new RaceEventAttribute();
        $raceEventAttribute->setName('Attr 1');

        $manager->persist($raceEventAttribute);
        $this->addReference('RaceEventAttribute-1', $raceEventAttribute);

        $raceEventAttribute = new RaceEventAttribute();
        $raceEventAttribute->setName('Attr 2');

        $manager->persist($raceEventAttribute);
        $this->addReference('RaceEventAttribute-2', $raceEventAttribute);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

    // public function getDependencies()
    // {
    //     return [
    //     ];
    // }
}
