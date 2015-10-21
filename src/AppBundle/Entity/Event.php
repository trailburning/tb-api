<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

/**
 * Event.
 *
 * @ORM\Table(name="api_event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\EventRepository")
 * @SWG\Definition(required={"id", "name", "about", "coords"}, @SWG\Xml(name="Event"))
 * @Serializer\ExclusionPolicy("all")
 */
class Event
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
     * @SWG\Property(@SWG\Xml(name="id"))
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
     * @var Journey
     *
     * @ORM\ManyToOne(targetEntity="Journey", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     */
    private $journey;
    
    /**
     * @var Point point
     *
     * @ORM\Column(name="coords", type="point", columnDefinition="GEOMETRY(POINT,4326)")
     * @SWG\Property(type="Array")
     */
    private $coords;
    
    /**
     * @var Asset[]
     *
     * @ORM\OneToMany(targetEntity="Asset", mappedBy="event")
     * @SWG\Property()
     * @Serializer\Expose
     */
    private $assets;
    
    /**
     * @var EventCustom[]
     *
     * @ORM\OneToMany(targetEntity="EventCustom", mappedBy="event", cascade={"persist", "remove"})
     */
    private $customFields;

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
     * @Serializer\SerializedName("coords")
     * @return array
     */
    public function getCoordsAsArray() 
    {
        if ($this->coords === null) {
            return [];
        }
        
        return [
            $this->coords->getLongitude(),
            $this->coords->getLatitude(),
        ];
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("custom")
     * @return array
     */
    public function getCustomFieldsArray() 
    {
        $fields = [];
        foreach ($this->getCustomFields() as $customField) {
            $fields[$customField->getKey()] = $customField->getValue();
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
    
    /**
     * @param point $coords
     * @return self
     */
    public function setCoords($coords)
    {
        $this->coords = $coords;
    
        return $this;
    }

    /**
     * @return point 
     */
    public function getCoords()
    {
        return $this->coords;
    }
    
    /**
     * @param Asset $assets
     * @return self
     */
    public function addAsset(Asset $asset)
    {
        $this->assets[] = $assets;

        return $this;
    }
    
    /**
     * @param Asset $assets
     */
    public function removeAsset(Asset $asset)
    {
        $this->assets->removeElement($asset);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssets()
    {
        return $this->assets;
    }
    
    /**
     * @param EventCustom $eventCustom
     * @return self
     */
    public function addCustomField(EventCustom $eventCustom)
    {
        $eventCustom->setEvent($this);
        $this->customFields[] = $eventCustom;

        return $this;
    }
    
    /**
     * @param EventCustom $eventCustom
     */
    public function removeCustomField(EventCustom $eventCustom)
    {
        $this->customFields->removeElement($eventCustom);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCustomFields()
    {
        return $this->customFields;
    }
}
