<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\Region;
use CrEOF\Spatial\PHP\Types\Geometry\Point;

/**
 * Region data fixtures.
 */
class RegionData extends AbstractFixture implements FixtureInterface//, DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $region = new Region();
        $region->setName('Germany');
        $region->setText('Germany');
        $region->setMapboxID('country.1');
        $region->setType('country');
        $region->setCoords(new Point(7.7491, 46.0207, 4326));
        $region->setBboxRadius(1000000);
        $manager->persist($region);
        $this->addReference('Region-1', $region);
        
        $region = new Region();
        $region->setName('Berlin, Germany');
        $region->setText('Berlin');
        $region->setMapboxID('region.19999799996200520');
        $region->setType('region');
        $region->setBboxRadius(29489);
        $region->setCoords(new Point(13.393236, 52.504043, 4326));
        $manager->persist($region);
        $this->addReference('Region-2', $region);
        
        $region = new Region();
        $region->setName('Berlin, Berlin, Germany');
        $region->setText('Berlin');
        $region->setMapboxID('place.9603');
        $region->setType('region');
        $region->setBboxRadius(29489);
        $region->setCoords(new Point(13.4049, 52.52, 4326));
        $manager->persist($region);
        $this->addReference('Region-3', $region);

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
