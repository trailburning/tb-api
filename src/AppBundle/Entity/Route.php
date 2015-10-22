<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

/**
 * Route.
 *
 * @ORM\Table(name="api_route")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RouteRepository")
 * @SWG\Definition(required={"coords"}, @SWG\Xml(name="Route"))
 * @Serializer\ExclusionPolicy("all")
 */
class Route
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
     * @var Point point
     *
     * @ORM\Column(name="coords", type="point", columnDefinition="GEOMETRY(POINT,4326)")
     * @SWG\Property(type="Array")
     */
    private $coords;
    
    /**
     * @var Journey
     *
     * @ORM\ManyToOne(targetEntity="Journey", inversedBy="routes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $journey;

    /**
     * ################################################################################################################
     *
     *                                         User Defined
     *
     * ################################################################################################################
     */
    
    public function __construct($coords) {
        $this->setCoords($coords);
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
