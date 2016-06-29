<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * RaceEvent.
 *
 * @ORM\Table(name="api_race_event")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceEventRepository")
 * @SWG\Definition(required={"id", "name", "coords"}, @SWG\Xml(name="RaceEvent"))
 * @Serializer\ExclusionPolicy("all")
 */
class RaceEvent
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
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
     * @ORM\Column(name="name", type="string", length=255)
     * @SWG\Property()
     * @Serializer\Expose
     * @Assert\NotBlank()
     * @Assert\Length(max = "255")
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="about", type="text", nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     */
    private $about;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     */
    private $website;

    /**
     * @var Point point
     *
     * @ORM\Column(name="coords", type="point", columnDefinition="GEOMETRY(POINT,4326)")
     * @SWG\Property(type="Array")
     * @Assert\NotBlank()
     */
    private $coords;
    
    /**
     * @var Race[]
     *
     * @ORM\OneToMany(targetEntity="Race", mappedBy="raceEvent", cascade={"persist", "remove"})
     * @SWG\Property()
     * @Serializer\Expose
     * @ORM\OrderBy({"date" = "ASC"})
     */
    private $races;
    
    /**
     * @var string
     *
     * @ORM\Column(name="location", type="text", nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     */
    private $location;
    
    /**
     * @var Region
     *
     * @ORM\ManyToOne(targetEntity="Region", inversedBy="raceEvents")
     * @ORM\JoinColumn(nullable=true)
     */
    private $region;

    /**
     * ################################################################################################################.
     *
     *                                         User Defined
     *
     * ################################################################################################################
     */
    public function __construct()
    {
        $this->oid = str_replace('.', '', uniqid(null, true));
        $this->races = new ArrayCollection();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("coords")
     *
     * @return array
     */
    public function getCoordsAsArray()
    {
        return [
            $this->coords->getLongitude(),
            $this->coords->getLatitude(),
        ];
    }
    
    /**
     * ################################################################################################################.
     *
     *                                         Getters and Setters
     *
     * ################################################################################################################
     */

    /**
     * Get id.
     *
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
     * Set name.
     *
     * @param string $name
     *
     * @return RaceEvent
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name.
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set about.
     *
     * @param string $about
     *
     * @return RaceEvent
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about.
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set website.
     *
     * @param string $website
     *
     * @return RaceEvent
     */
    public function setWebsite($website)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website.
     *
     * @return string
     */
    public function getWebsite()
    {
        return $this->website;
    }

    /**
     * @param point $coords
     *
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
     * @param Race $races
     * @return self
     */
    public function addRace(Race $race)
    {
        $race->setAsset($this);
        $this->races[] = $race;

        return $this;
    }

    /**
     * @param Race $race
     */
    public function removeRace(Race $race)
    {
        $this->races->removeElement($race);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRaces()
    {
        return $this->races;
    }
    
    /**
     * @param string $location
     *
     * @return self
     */
    public function setLocation($location)
    {
        $this->location = $location;

        return $this;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }
    
    /**
     * @param Region $region
     * @return self
     */
    public function setRegion(Region $region)
    {
        $this->region = $region;
    
        return $this;
    }

    /**
     * @return Region
     */
    public function getRegion()
    {
        return $this->region;
    }
}
