<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\DBAL\Types\MIMEType;
use AppBundle\Entity\Asset;
use AppBundle\Entity\Media;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class AssetData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    public function load(ObjectManager $manager)
    {
        $asset = new Asset();
        $asset->setName('Test Asset 1');
        $asset->setAbout('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
        $asset->setCategory($this->getReference('AssetCategory-expedition'));
        $asset->setEvent($this->getReference('Event-1'));

        $media = new Media();
        $media->setPath('http://tbmedia2.imgix.net/test25zero/test.jpg');
        $media->setMimeType(MIMEType::JPEG);
        $asset->addMedia($media);

        $media = new Media();
        $media->setPath('http://tbmedia2.imgix.net/test25zero/test2.jpg');
        $media->setMimeType(MIMEType::JPEG);
        $asset->addMedia($media);

        $manager->persist($asset);

        $asset = new Asset();
        $asset->setName('Test Asset 2');
        $asset->setAbout('Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.');
        $asset->setCategory($this->getReference('AssetCategory-expedition'));
        $asset->setEvent($this->getReference('Event-1'));

        $media = new Media();
        $media->setPath('http://tbmedia2.imgix.net/test25zero/test.jpg');
        $media->setMimeType(MIMEType::JPEG);
        $asset->addMedia($media);

        $media = new Media();
        $media->setPath('http://tbmedia2.imgix.net/test25zero/test2.jpg');
        $media->setMimeType(MIMEType::JPEG);
        $asset->addMedia($media);

        $manager->persist($asset);

        $manager->flush();
    }

    public function getOrder()
    {
        return 1;
    }

    public function getDependencies()
    {
        return [
            'AppBundle\DataFixtures\ORM\EventData',
            'AppBundle\DataFixtures\ORM\AssetCategoryData',
        ];
    }
}
