<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\Journey;
use AppBundle\Entity\RoutePoint;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class JourneyData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    
    public function load(ObjectManager $manager)
    {
        $journey = new Journey();
        $journey->setName('Test Journey 1');
        $journey->setAbout('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
        $journey->setPublish(true);
        $journey->setUser($this->getReference('User-matt'));
        $journey->addRoutePoint(new RoutePoint(new Point(13.221316, 52.489695, 4326)));
        $journey->addRoutePoint(new RoutePoint(new Point(13.221316, 52.489695, 4326)));
        $journey->addRoutePoint(new RoutePoint(new Point(13.221316, 52.489695, 4326)));
        
        $manager->persist($journey);
        $this->addReference('Journey-1', $journey);
        
        $journey = new Journey();
        $journey->setName('Test Journey 2');
        $journey->setAbout('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
        $journey->setPublish(true);
        $journey->setUser($this->getReference('User-matt'));
        $journey->addRoutePoint(new RoutePoint(new Point(13.221316, 52.489695, 4326)));
        $journey->addRoutePoint(new RoutePoint(new Point(13.221316, 52.489695, 4326)));
        $journey->addRoutePoint(new RoutePoint(new Point(13.221316, 52.489695, 4326)));

        $manager->persist($journey);
        $this->addReference('Journey-2', $journey);
        
        $journey = new Journey();
        $journey->setName('Test Journey 3');
        $journey->setAbout('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
        $journey->setPublish(true);
        $journey->setUser($this->getReference('User-matt'));
        $journey->addRoutePoint(new RoutePoint(new Point(13.221316, 52.489695, 4326)));
        $journey->addRoutePoint(new RoutePoint(new Point(13.221316, 52.489695, 4326)));
        $journey->addRoutePoint(new RoutePoint(new Point(13.221316, 52.489695, 4326)));
        
        $manager->persist($journey);
        $this->addReference('Journey-3', $journey);
        
        $journey = new Journey();
        $journey->setName('Unpublished Journey');
        $journey->setAbout('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
        $journey->setPublish(false);
        $journey->setUser($this->getReference('User-matt'));
        $journey->addRoutePoint(new RoutePoint(new Point(13.221316, 52.489695, 4326)));
        $journey->addRoutePoint(new RoutePoint(new Point(13.221316, 52.489695, 4326)));
        $journey->addRoutePoint(new RoutePoint(new Point(13.221316, 52.489695, 4326)));
        
        $manager->persist($journey);
        $this->addReference('Journey-4', $journey);
        
        $manager->flush();
    }
    
    public function getOrder()
    {
        return 1;
    }
    
    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\UserData',
        ];
    }
}