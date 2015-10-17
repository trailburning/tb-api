<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * Media.
 *
 * @ORM\Table(name="api_media")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MediaRepository")
 * @SWG\Definition(required={"id", "type", "path"}, @SWG\Xml(name="Media"))
 * @Serializer\ExclusionPolicy("all")
 */
class Media
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
    private $path;
    
    /**
     * @var string
     *
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\MediaType")
     * @ORM\Column(type="MediaType")
     * @SWG\Property(enum={"image"})
     * @Serializer\Expose
     */
    private $type;
    
    /**
      * @ORM\ManyToOne(targetEntity="Asset", inversedBy="medias")
      */
    protected $asset;

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
     * @param string $path
     *
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
    
    /**
     * @param string $type
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }
    
    /**
     * @param Asset $asset
     * @return self
     */
    public function setAsset(Asset $asset)
    {
        $this->asset = $asset;
    
        return $this;
    }

    /**
     * @return Asset
     */
    public function getAsset()
    {
        return $this->asset;
    }
}
