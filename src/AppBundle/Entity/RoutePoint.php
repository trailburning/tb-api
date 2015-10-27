<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

/**
 * RoutePoint.
 *
 * @ORM\Table(name="api_route_point")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RoutePointRepository")
 * @SWG\Definition(required={"coords"}, @SWG\Xml(name="RoutePoint"))
 * @Serializer\ExclusionPolicy("all")
 */
class RoutePoint
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
     * @SWG\Property()
     */
    private $coords;
    
    /**
     * @var Journey
     *
     * @ORM\ManyToOne(targetEntity="Journey", inversedBy="routePoints")
     * @ORM\JoinColumn(nullable=false)
     */
    private $journey;
    
    /**
     * @var float
     *
     * @ORM\Column(type="float", nullable=true)
     */
    private $elevation;

    /**
     * ################################################################################################################
     *
     *                                         User Defined
     *
     * ################################################################################################################
     */
    
    public function __construct($coords, $elevation = null) {
        $this->setCoords($coords);
        $this->setElevation($elevation);
    }   
    
    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("coords")
     * @return array
     */
    public function getDataAsArray() 
    {
        if ($this->coords === null) {
            return [];
        }
        
        $data = [
            $this->coords->getLongitude(),
            $this->coords->getLatitude(),
        ];
        
        if ($this->getElevation() !== null) {
            $data[] = $this->getElevation();
        }
        
        return $data;
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
    
    /**
     * @param float
     * @return self
     */
    public function setElevation($elevation)
    {
        $this->elevation = $elevation;
    
        return $this;
    }

    /**
     * @return float
     */
    public function getElevation()
    {
        return (float)$this->elevation;
    }
}
