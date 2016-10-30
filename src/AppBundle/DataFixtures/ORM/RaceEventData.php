<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\RaceEvent;
use AppBundle\Entity\RaceEventAttribute;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use AppBundle\Entity\Media;
use AppBundle\DBAL\Types\MIMEType;

/**
 * RaceEvent data fixtures.
 */
class RaceEventData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $raceEvent = new RaceEvent();
        $raceEvent->setName('Engadin Ultraks');
        $raceEvent->setAbout('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
        $raceEvent->setWebsite('http://www.trailburning.com');
        $raceEvent->setCoords(new Point(7.7491, 46.0207, 4326));
        $raceEvent->setLocation('Zermatt, Switzerland');
        $raceEvent->setType('road_run');
        $raceEvent->setAttributes([
            $this->getReference('RaceEventAttribute-1'),
            $this->getReference('RaceEventAttribute-2'),
        ]);

        $media = new Media();
        $media->setPath('http://tbmedia2.imgix.net/test25zero/test.jpg');
        $media->setMimeType(MIMEType::JPEG);
        $raceEvent->addMedia($media);

        $manager->persist($raceEvent);
        $this->addReference('RaceEvent-1', $raceEvent);

        $raceEvent = new RaceEvent();
        $raceEvent->setName('Berlin Marathon');
        $raceEvent->setAbout('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
        $raceEvent->setWebsite('http://www.trailburning.com');
        $raceEvent->setCoords(new Point(13.221316, 52.489695, 4326));
        $raceEvent->setLocation('Berlin, Berlin, Germany');
        $raceEvent->setType('road_run');
        $raceEvent->setAttributes([
            $this->getReference('RaceEventAttribute-1'),
            $this->getReference('RaceEventAttribute-2'),
        ]);
        $manager->persist($raceEvent);
        $this->addReference('RaceEvent-2', $raceEvent);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\RaceEventAttributeData',
        ];
    }
}
