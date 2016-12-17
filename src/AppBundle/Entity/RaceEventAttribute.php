<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * MediaAttribute.
 *
 * @ORM\Table(name="api_race_event_attribue")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceEventAttributeRepository")
 * @SWG\Definition(required={"id", "name"}, @SWG\Xml(name="RaceEventAttribute"))
 * @Serializer\ExclusionPolicy("all")
 */
class RaceEventAttribute
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent"})
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent"})
     */
    private $name;
    
    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"name"}, updatable=false)
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $slug;

    /**
     * @var RaceEvents[]
     *
     * @ORM\ManyToMany(targetEntity="RaceEvent", mappedBy="attributes")
     */
    private $raceEvents;

    /**
     * ################################################################################################################.
     *
     *                                         User Defined
     *
     * ################################################################################################################
     */
    public function __construct($name = null)
    {
        $this->setName($name);
        $this->raceEvents = new ArrayCollection();
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
     * @param RaceEvent $raceEvent
     *
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
     * @param string $slug
     *
     * @return self
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }
}
