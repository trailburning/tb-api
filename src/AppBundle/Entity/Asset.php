<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Asset.
 *
 * @ORM\Table(name="api_asset")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssetRepository")
 * @SWG\Definition(required={"id", "about", "category"}, @SWG\Xml(name="Asset"))
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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Assert\Length(max = "255")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @SWG\Property()
     * @Serializer\Expose
     * @Assert\NotBlank()
     */
    private $about;
    
    /**
     * @var Media[]
     *
     * @ORM\OneToMany(targetEntity="Media", mappedBy="asset", cascade={"persist", "remove"})
     * @SWG\Property(property="media")
     * @Serializer\Expose
     * @Serializer\SerializedName("media")
     */
    private $medias;
    
    /**
     * @var Event
     *
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="assets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $event;
    
    /**
     * @var AssetCategory 
     *
     * @ORM\ManyToOne(targetEntity="AssetCategory", inversedBy="assets")
     * @SWG\Property()
     * @Serializer\Expose
     * @Assert\NotBlank()
     */
    private $category;
    
    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     */
    private $position;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Assert\Length(max = "255")
     */
    private $credit;

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
        $this->medias = new ArrayCollection();
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
     * @param Media $medias
     * @return self
     */
    public function addMedia(Media $media)
    {
        $media->setAsset($this);
        $this->medias[] = $media;

        return $this;
    }

    /**
     * @param Media $medias
     */
    public function removeMedia(Media $media)
    {
        $this->medias->removeElement($media);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getMedias()
    {
        return $this->medias;
    }
    
    /**
     * @param Event $event
     * @return self
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    
        return $this;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }
    
    /**
     * @param int $position
     *
     * @return self
     */
    public function setPosition($position)
    {
        $this->position = $position;
        
        return $this;
    }

    /**
     * @return int
     */
    public function getPosition()
    {
        return $this->position;
    }
        
    /**
     * @param string $credit
     *
     * @return self
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;
        
        return $this;
    }

    /**
     * @return string
     */
    public function getCredit()
    {
        return $this->credit;
    }
}
