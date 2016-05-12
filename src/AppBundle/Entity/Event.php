<?php

namespace AppBundle\Entity;

use Burgov\Bundle\KeyValueFormBundle\KeyValueContainer;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @var Journey
     *
     * @Gedmo\SortableGroup
     * @ORM\ManyToOne(targetEntity="Journey", inversedBy="events")
     * @ORM\JoinColumn(nullable=false)
     * @Assert\NotBlank()
     */
    private $journey;

    /**
     * @var Point point
     *
     * @ORM\Column(name="coords", type="point", columnDefinition="GEOMETRY(POINT,4326)")
     * @SWG\Property(type="Array")
     * @Assert\NotBlank()
     */
    private $coords;

    /**
     * @var Asset[]
     *
     * @ORM\OneToMany(targetEntity="Asset", mappedBy="event", cascade={"persist", "remove"})
     * @SWG\Property()
     * @Serializer\Expose
     * @ORM\OrderBy({"position" = "ASC"})
     */
    private $assets;

    /**
     * @var EventCustom[]
     *
     * @ORM\OneToMany(targetEntity="EventCustom", mappedBy="event", cascade={"persist", "remove"})
     */
    private $custom;

    /**
     * @Gedmo\SortablePosition
     * @ORM\Column(name="position", type="integer")
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
        $this->assets = new ArrayCollection();
        $this->custom = new ArrayCollection();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("coords")
     *
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
     * @SWG\Property(property="custom")
     *
     * @return array
     */
    public function getCustomAPIResponse()
    {
        if (count($this->getCustom()) === 0) {
            return;
        }

        return $this->getCustom();
    }

    /**
     * Set the custom fields.
     *
     * @param array|KeyValueContainer|\Traversable $data Something that can be converted to an array.
     */
    public function setCustom($customFields)
    {
        $this->custom = new ArrayCollection();
        $customFields = $this->convertToArray($customFields);
        foreach ($customFields as $key => $value) {
            $this->addCustom(new EventCustom($key, $value));
        }
    }

    /**
     * Extract an array out of $data or throw an exception if not possible.
     *
     * @param array|KeyValueContainer|\Traversable $data Something that can be converted to an array.
     *
     * @return array Native array representation of $data
     *
     * @throws InvalidArgumentException If $data can not be converted to an array.
     */
    private function convertToArray($data)
    {
        if (is_array($data)) {
            return $data;
        }

        if ($data instanceof KeyValueContainer) {
            return $data->toArray();
        }

        if ($data instanceof \Traversable) {
            return iterator_to_array($data);
        }

        throw new \Exception(sprintf('Expected array, Traversable or KeyValueContainer, got "%s"', is_object($data) ? getclass($data) : get_type($data)));
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
     * @param Journey $journey
     *
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
     * @param Asset $assets
     *
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
     *
     * @return self
     */
    public function addCustom(EventCustom $eventCustom)
    {
        $eventCustom->setEvent($this);
        $this->custom[] = $eventCustom;

        return $this;
    }

    /**
     * @param EventCustom $eventCustom
     */
    public function removeCustom(EventCustom $eventCustom)
    {
        $this->custom->removeElement($eventCustom);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCustom()
    {
        $map = [];
        foreach ($this->custom as $customField) {
            $map[$customField->getKey()] = $customField->getValue();
        }

        return $map;
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
