<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Media.
 *
 * @ORM\Table(name="api_media")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MediaRepository")
 * @SWG\Definition(required={"id", "mimeType", "path"}, @SWG\Xml(name="Media"))
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
     */
    private $id;
    
    /**
     * @var string
     * @ORM\Column(type="string", length=22, unique=true)
     * @SWG\Property(property="id")
     * @Serializer\Expose
     * @Serializer\SerializedName("id")
     */
    private $oid;

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
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\MIMEType")
     * @ORM\Column(type="MIMEType")
     * @SWG\Property()
     * @Serializer\Expose
     */
    private $mimeType;
    
    /**
     * @ORM\ManyToOne(targetEntity="Asset", inversedBy="medias")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $asset;
    
    /**
     * @var MediaAttribute[]
     *
     * @ORM\OneToMany(targetEntity="MediaAttribute", mappedBy="media", cascade={"persist", "remove"})
     */
    private $attributes;

    /**
     * ################################################################################################################
     *
     *                                         User Defined
     *
     * ################################################################################################################
     */

    public function __construct()
    {
        $this->oid = str_replace('.', '', uniqid(null, true));
    }
    
    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("metadata")
     * @SWG\Property(property="metadata")
     * @return array
     */
    public function getMetadata() 
    {
        if (count($this->getAttributes()) === 0) {
            return null;
        }
        
        $fields = [];
        foreach ($this->getAttributes() as $attribute) {
            $fields[$attribute->getKey()] = $attribute->getValue();
        }
        
        return $fields;
    }

    /**
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
     * @return string
     */
    public function getOid()
    {
        return $this->oid;
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
     * @param string $mimeType
     * @return self
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;
    
        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
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
    
    /**
     * @param MediaAttribute $attribute
     * @return self
     */
    public function addAttribute(MediaAttribute $attribute)
    {
        $attribute->setMedia($this);
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * @param MediaAttribute $attribute
     */
    public function removeAttribute(MediaAttribute $attribute)
    {
        $this->medias->removeElement($attribute);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAttributes()
    {
        return $this->attributes;
    }
    
    /**
     * @param MediaAttribute $attribute
     * @return self
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }

        return $this;
    }
}
