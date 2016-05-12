<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Event;
use AppBundle\Entity\EventCustom;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class EventData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    
    public function load(ObjectManager $manager)
    {
        $event = new Event();
        $event->setName('Test Event 1');
        $event->setAbout('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
        $event->setJourney($this->getReference('Journey-1'));
        $event->addCustom(new EventCustom('custom1', 'custom text'));
        $event->addCustom(new EventCustom('custom2', 'custom text'));
        $event->setCoords(new Point(13.221316, 52.489695, 4326));
        $this->addReference('Event-1', $event);
        $manager->persist($event);
        
        $event = new Event();
        $event->setName('Test Event 2');
        $event->setAbout('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
        $event->setJourney($this->getReference('Journey-2'));
        $event->addCustom(new EventCustom('custom1', 'custom text'));
        $event->addCustom(new EventCustom('custom2', 'custom text'));
        $event->setCoords(new Point(13.221316, 52.489695, 4326));
        $this->addReference('Event-2', $event);
        $manager->persist($event);
        
        $event = new Event();
        $event->setName('Test Event 3');
        $event->setAbout('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
        $event->setJourney($this->getReference('Journey-3'));
        $event->addCustom(new EventCustom('custom1', 'custom text'));
        $event->addCustom(new EventCustom('custom2', 'custom text'));
        $event->setCoords(new Point(13.221316, 52.489695, 4326));
        $this->addReference('Event-3', $event);
        $manager->persist($event);
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 1;
    }
    
    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\JourneyData',
        ];
    }
}