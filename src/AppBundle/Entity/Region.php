<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * Region
 *
 * @ORM\Table(name="api_region", uniqueConstraints={@ORM\UniqueConstraint(name="unique_mapbox_id", columns={"mapbox_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RegionRepository")
 */
class Region
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(name="text", type="string", length=255, nullable=true)
     */
    private $text;
    
    /**
     * @var string
     *
     * @ORM\Column(name="bbox_radius", type="integer", nullable=true)
     */
    private $bboxRadius;
    
    /**
     * @var string
     *
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\RegionType")
     * @ORM\Column(type="RegionType", nullable=true)
     */
    private $type;
    
    /**
     * @var string
     *
     * @ORM\Column(name="mapbox_id", type="string", length=255)
     */
    private $mapboxID;

    /**
     * @var Point point
     *
     * @ORM\Column(name="coords", type="point", columnDefinition="GEOMETRY(POINT,4326)")
     * @Assert\NotBlank()
     */
    private $coords;

    /**
     * @var RaceEvents[]
     *
     * @ORM\ManyToMany(targetEntity="RaceEvent", mappedBy="regions")
     */
    private $raceEvents;

    /**
     * ################################################################################################################.
     *
     *                                         User Defined
     *
     * ################################################################################################################
     */
    public function __construct()
    {
        $this->raceEvents = new ArrayCollection();
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
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Region
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set text
     *
     * @param string $text
     * @return self
     */
    public function setText($text)
    {
        $this->text = $text;

        return $this;
    }

    /**
     * Get text
     *
     * @return string 
     */
    public function getText()
    {
        return $this->text;
    }
    
    /**
     * Set mapboxID
     *
     * @param string $mapboxID
     * @return Region
     */
    public function setMapboxID($mapboxID)
    {
        $this->mapboxID = $mapboxID;

        return $this;
    }

    /**
     * Get mapboxID
     *
     * @return string 
     */
    public function getMapboxID()
    {
        return $this->mapboxID;
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
     * @param RaceEvent $raceEvent
     * @return self
     */
    public function addRaceEvent(RaceEvent $raceEvent)
    {
        $this->raceEvents[] = $raceEvent;

        return $this;
    }

    /**
     * @param RaceEvent $raceEvent
     */
    public function removeRaceEvent(RaceEvent $raceEvent)
    {
        $this->raceEvents->removeElement($raceEvent);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRaceEvents()
    {
        return $this->raceEvents;
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
     * @param string $bboxRadius
     * @return self
     */
    public function setBboxRadius($bboxRadius)
    {
        $this->bboxRadius = $bboxRadius;
    
        return $this;
    }

    /**
     * @return string
     */
    public function getBboxRadius()
    {
        return $this->bboxRadius;
    }
}
