<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Journey.
 *
 * @ORM\Table(name="api_journey")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JourneyRepository")
 * @SWG\Definition(required={"id", "name", "about"}, @SWG\Xml(name="Journey"))
 * @Serializer\ExclusionPolicy("all")
 */
class Journey
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
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $publish = false;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="journeys")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $user;

    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="journey", cascade={"persist", "remove"})
     * @ORM\OrderBy({"position" = "ASC"})
     */
    protected $events;

    /**
     * @var RoutePoint[]
     *
     * @ORM\OneToMany(targetEntity="RoutePoint", mappedBy="journey", cascade={"persist", "remove"})
     * @SWG\Property(property="route_points")
     * @Serializer\Expose
     */
    private $routePoints;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
     * @Assert\Type(type="integer")
     */
    private $position;

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
        $this->routePoints = new ArrayCollection();
        $this->events = new ArrayCollection();
    }

    /**
     * ################################################################################################################.
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
     * @param bool $publish
     *
     * @return self
     */
    public function setPublish($publish)
    {
        if ($publish === null) {
            return $this;
        }
        
        $this->publish = $publish;

        return $this;
    }

    /**
     * @return bool
     */
    public function isPublish()
    {
        return $this->publish;
    }

    /**
     * @param User $user
     *
     * @return self
     */
    public function setUser(User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param Event $events
     */
    public function removeEvent(Event $event)
    {
        $this->events->removeElement($event);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getEvents()
    {
        return $this->events;
    }

    /**
     * @param RoutePoint $routePoints
     *
     * @return self
     */
    public function addRoutePoint(RoutePoint $routePoint)
    {
        $routePoint->setJourney($this);
        $this->routePoints[] = $routePoint;

        return $this;
    }

    /**
     * @param RoutePoint $routePoints
     */
    public function removeRoutePoint(RoutePoint $routePoint)
    {
        $this->routePoints->removeElement($routePoint);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getRoutePoints()
    {
        return $this->routePoints;
    }

    /**
     */
    public function clearRoutePoints()
    {
        $this->routePoints->clear();
    }

    /**
     */
    public function setNullRoutePoints()
    {
        $this->routePoints = null;
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
}
