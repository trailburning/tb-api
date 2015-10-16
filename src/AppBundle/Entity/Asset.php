<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * Journey.
 *
 * @ORM\Table(name="api_asset")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssetRepository")
 * @SWG\Definition(required={"id", "name", "about", "category"}, @SWG\Xml(name="Asset"))
 * @Serializer\ExclusionPolicy("all")
 */
class Asset
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @SWG\Property(format="int32")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @SWG\Property()
     * @Serializer\Expose
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @SWG\Property()
     * @Serializer\Expose
     */
    private $about;
    
    /**
     * @var string
     *
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\AssetCategoryType")
     * @ORM\Column(name="category", type="AssetCategoryType", nullable=false)
     * @SWG\Property(enum={"Expedition","Flora","Fauna","Mountain","Time Capsule"})
     * @Serializer\Expose
     */
    private $category;
    
    /**
      * @ORM\ManyToOne(targetEntity="Media", inversedBy="assets")
      */
    protected $media;
    
    /**
      * @ORM\ManyToOne(targetEntity="Journey", inversedBy="assets")
      */
    protected $journey;

    /*
     * ################################################################################################################
     *
     *                                         User Defined
     *
     * ################################################################################################################
     */


    /*
     * ################################################################################################################
     *
     *                                         Getters and Setters
     *
     * ################################################################################################################
     */

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $name
     *
     * @return self
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $about
     *
     * @return self
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }
    
    /**
     * @param string $category
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * @param Media $media
     * @return self
     */
    public function setMedia(Media $media)
    {
        $this->media = $media;
    
        return $this;
    }

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }
    
    /**
     * @param Journey $journey
     * @return self
     */
    public function setJourney(Journey $journey)
    {
        $this->journey = $journey;
    
        return $this;
    }

    /**
     * @return Journey
     */
    public function getJourney()
    {
        return $this->journey;
    }
}
