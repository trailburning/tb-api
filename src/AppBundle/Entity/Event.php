<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

/**
 * Journey.
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
     * @ORM\Column(type="string", length=22, unique=true, nullable=true)
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
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Journey", inversedBy="events")
     */
    private $journey;
    
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $journeyId;
    
    /**
     * @var Point point
     *
     * @ORM\Column(name="coords", type="point", columnDefinition="GEOMETRY(POINT,4326)")
     */
    private $coords;
    
    /**
     * @var Asset[]
     *
     * @ORM\OneToMany(targetEntity="Asset", mappedBy="event")
     * @SWG\Property()
     * @Serializer\Expose
     */
    protected $assets;

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
}
