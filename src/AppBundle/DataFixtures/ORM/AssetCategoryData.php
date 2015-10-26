<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\AssetCategory;

class AssetCategoryData extends AbstractFixture implements FixtureInterface
{
    
    public function load(ObjectManager $manager)
    {
        $category = new AssetCategory();
        $category->setName('expedition');
        $category->setLabel('Expedition');
        $manager->persist($category);
        $this->addReference('AssetCategory-expedition', $category);
        
        $category = new AssetCategory();
        $category->setName('fauna');
        $category->setLabel('Fauna');
        $manager->persist($category);
        $this->addReference('AssetCategory-fauna', $category);
        
        $category = new AssetCategory();
        $category->setName('flora');
        $category->setLabel('Flora');
        $manager->persist($category);
        $this->addReference('AssetCategory-flora', $category);
        
        $category = new AssetCategory();
        $category->setName('mountain');
        $category->setLabel('Mountain');
        $this->addReference('AssetCategory-mountain', $category);
        $manager->persist($category);
        
        $category = new AssetCategory();
        $category->setName('timecapsule');
        $category->setLabel('Time Capsule');
        $this->addReference('AssetCategory-timecapsule', $category);
        $manager->persist($category);

        $manager->flush();
    }
    
    public function getOrder()
    {
        return 1;
    }
}