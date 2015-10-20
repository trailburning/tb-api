<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

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
     * @ORM\Column(type="string", length=22, unique=true, nullable=true)
     * @SWG\Property()
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
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     */
    private $publish = false;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="journeys")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;
    
    /**
     * @ORM\OneToMany(targetEntity="Event", mappedBy="journey")
     */
    protected $events;

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
     * @param bool $publish
     *
     * @return self
     */
    public function setPublish($publish)
    {
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
}
