<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * Race
 *
 * @ORM\Table(name="api_race")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceRepository")
 * @SWG\Definition(required={"id", "name"}, @SWG\Xml(name="Race"))
 * @Serializer\ExclusionPolicy("all")
 */
class Race
{
    /**
     * @var integer
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
     * @Serializer\Groups({"raceEvent", "user"})
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
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date", type="date")
     */
    private $date;

    /**
     * @var integer
     *
     * @ORM\Column(type="integer", nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $distance;

    /**
     * @var string
     *
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\RaceCategory")
     * @ORM\Column(type="RaceCategory", nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $category;
    
    /**
     * @var RaceEvent
     *
     * @ORM\ManyToOne(targetEntity="RaceEvent", inversedBy="races")
     * @ORM\JoinColumn(nullable=false)
     */
    private $raceEvent;

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
    }
    
    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("date")
     * @Serializer\Groups({"raceEvent", "user"})
     *
     * @return string
     */
    public function getDateAsString()
    {
        if ($this->date === null) {
            return "";
        }

        return $this->date->format('Y-m-d');
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
     * Set name
     *
     * @param string $name
     * @return Race
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
     * Set date
     *
     * @param \DateTime $date
     * @return Race
     */
    public function setDate($date)
    {
        $this->date = $date;

        return $this;
    }

    /**
     * Get date
     *
     * @return \DateTime 
     */
    public function getDate()
    {
        return $this->date;
    }

    /**
     * Set distance
     *
     * @param string $distance
     * @return Race
     */
    public function setDistance($distance)
    {
        $this->distance = $distance;

        return $this;
    }

    /**
     * Get distance
     *
     * @return string 
     */
    public function getDistance()
    {
        return $this->distance;
    }
    
    /**
     * Set category
     *
     * @param string $category
     * @return Race
     */
    public function setCategory($category)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return string 
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * @param RaceEvent $raceEvent
     * @return self
     */
    public function setRaceEvent(RaceEvent $raceEvent)
    {
        $this->raceEvent = $raceEvent;
    
        return $this;
    }

    /**
     * @return RaceEvent
     */
    public function getRaceEvent()
    {
        return $this->raceEvent;
    }
}
