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
        
        $category = new AssetCategory();
        $category->setName('fauna');
        $category->setLabel('Fauna');
        
        $category = new AssetCategory();
        $category->setName('flora');
        $category->setLabel('Flora');
        
        $category = new AssetCategory();
        $category->setName('mountain');
        $category->setLabel('Mountain');
        
        $category = new AssetCategory();
        $category->setName('timecapsule');
        $category->setLabel('Time Capsule');
        
        $manager->persist($category);

        $manager->flush();
    }
    
    public function getOrder()
    {
        return 1;
    }
}