<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geography\Point;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\Common\Collections\ArrayCollection;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

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
     * @var string
     *
     * @ORM\Column(name="about", type="text", nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $about;

    /**
     * @var string
     *
     * @ORM\Column(name="website", type="string", length=255, nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $website;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $email;

    /**
     * @var Point point
     *
     * @ORM\Column(name="coords", type="point", columnDefinition="GEOMETRY(POINT,4326)")
     * @SWG\Property(type="Array")
     * @Assert\NotBlank()
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $coords;

    /**
     * @var Race[]
     *
     * @ORM\OneToMany(targetEntity="Race", mappedBy="raceEvent", cascade={"persist", "remove"})
     * @SWG\Property()
     * @Serializer\Expose
     * @ORM\OrderBy({"distance" = "ASC"})
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $races;

    /**
     * @var string
     *
     * @ORM\Column(name="location", type="text", nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
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
     * @ORM\ManyToMany(targetEntity="Region", inversedBy="raceEvents")
     * @ORM\JoinTable(name="api_race_event_region")
     */
    private $regions;

    /**
     * @ORM\ManyToMany(targetEntity="RaceEventAttribute", inversedBy="raceEvents")
     * @ORM\JoinTable(name="api_race_event_race_event_attribute")
     * @SWG\Property()
     */
    private $attributes;

    /**
     * @var string
     *
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\RaceEventType")
     * @ORM\Column(type="RaceEventType", nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $type;

    /**
     * @var Media[]
     *
     * @ORM\OneToMany(targetEntity="Media", mappedBy="raceEvent", cascade={"persist", "remove"})
     * @SWG\Property(property="media")
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $medias;

    /**
     * @var RaceEventCompleted[]
     *
     * @ORM\OneToMany(targetEntity="RaceEventCompleted", mappedBy="raceEvent", cascade={"persist", "remove"})
     * @SWG\Property()
     * @ORM\OrderBy({"id" = "DESC"})
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent"})
     */
    private $completed;

    /**
     * @var RaceEventWishlist[]
     *
     * @ORM\OneToMany(targetEntity="RaceEventWishlist", mappedBy="raceEvent", cascade={"persist", "remove"})
     * @SWG\Property()
     * @ORM\OrderBy({"id" = "DESC"})
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent"})
     */
    private $wishlist;

    /**
     * @var RaceEventDoing[]
     *
     * @ORM\OneToMany(targetEntity="RaceEventDoing", mappedBy="raceEvent", cascade={"persist", "remove"})
     * @SWG\Property()
     * @ORM\OrderBy({"id" = "DESC"})
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent"})
     */
    private $doing;

    /**
     * @var float
     *
     * @ORM\Column(name="rating", type="decimal", precision=2, scale=1, nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $rating;

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
        $this->regions = new ArrayCollection();
        $this->attributes = new ArrayCollection();
        $this->medias = new ArrayCollection();
        $this->medias = new ArrayCollection();
        $this->completed = new ArrayCollection();
        $this->wishlist = new ArrayCollection();
        $this->doing = new ArrayCollection();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("coords")
     * @Serializer\Groups({"raceEvent", "user"})
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
     * @return \DateTime|null
     */
    public function getStartDate()
    {
        $startDate = null;
        foreach ($this->getRaces() as $race) {
            if ($startDate === null || $race->getDate() < $startDate) {
                $startDate = $race->getDate();
            }
        }

        return $startDate;
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("attributes")
     * @SWG\Property(property="attributes")
     * @Serializer\Groups({"raceEvent", "user"})
     *
     * @return array
     */
    public function getAttributesArray()
    {
        $attributes = [];
        foreach ($this->getAttributes() as $attribute) {
            $attributes[] = $attribute->getName();
        }

        return $attributes;
    }

    /**
     * @return array
     */
    public function getAttributesSlugArray()
    {
        $attributes = [];
        foreach ($this->getAttributes() as $attribute) {
            $attributes[] = $attribute->getSlug();
        }

        return $attributes;
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
     * Set email.
     *
     * @param string $email
     *
     * @return RaceEvent
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email.
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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
     * @return Point
     */
    public function getCoords()
    {
        return $this->coords;
    }

    /**
     * @param Race $race
     *
     * @return self
     */
    public function addRace(Race $race)
    {
        $race->setRaceEvent($this);
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
     * @return Race[]
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
     *
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

    /**
     * @return ArrayCollection
     */
    public function getRegions()
    {
        return $this->regions;
    }

    /**
     * @param array $regions
     *
     * @return self
     */
    public function setRegions($regions)
    {
        $this->regions = new ArrayCollection($regions);

        return $this;
    }

    /**
     * @param Region $region
     *
     * @return self
     */
    public function addRegion(Region $region)
    {
        $this->regions->add($region);
        $region->addRaceEvent($this);

        return $this;
    }

    /**
     * @param Region $region
     *
     * @return self
     */
    public function removeRegion(Region $region)
    {
        $this->regions->removeElement($region);
        $region->removeRaceEvent($this);

        return $this;
    }

    /**
     * @param RaceEventAttribute $attribute
     *
     * @return self
     */
    public function addAttribute(RaceEventAttribute $attribute)
    {
        $attribute->addRaceEvent($this);
        $this->attributes->add($attribute);

        return $this;
    }

    /**
     * @param RaceEventAttribute $attribute
     */
    public function removeAttribute(RaceEventAttribute $attribute)
    {
        $this->attributes->removeElement($attribute);
        $attribute->removeRaceEvent($this);
    }

    public function clearAttributes()
    {
        $this->attributes = new ArrayCollection();
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     * @SWG\Property(property="attributes")
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     *
     * @return self
     */
    public function setAttributes(array $attributes)
    {
        $this->attributes = new ArrayCollection();

        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }

        return $this;
    }

    /**
     * Set type.
     *
     * @param string $type
     *
     * @return self
     */
    public function setType($type)
    {
        $this->type = $type;

        return $this;
    }

    /**
     * Get type.
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @param Media $media
     *
     * @return self
     */
    public function addMedia(Media $media)
    {
        $media->setRaceEvent($this);
        $this->medias[] = $media;

        return $this;
    }

    /**
     * @param Media $media
     */
    public function removeMedia(Media $media)
    {
        $this->medias->removeElement($media);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getMedias()
    {
        return $this->medias;
    }

    /**
     * @return RaceEventCompleted[]
     */
    public function getCompleted()
    {
        return $this->completed;
    }

    /**
     * @param RaceEventCompleted[] $completed
     *
     * @return self
     */
    public function setCompleted(array $completed): self
    {
        $this->completed = $completed;

        return $this;
    }

    /**
     * @return RaceEventWishlist[]
     */
    public function getWishlist()
    {
        return $this->wishlist;
    }

    /**
     * @param RaceEventWishlist[] $wishlist
     *
     * @return self
     */
    public function setWishlist(array $wishlist): self
    {
        $this->wishlist = $wishlist;

        return $this;
    }

    /**
     * @return RaceEventDoing[]
     */
    public function getDoing()
    {
        return $this->doing;
    }

    /**
     * @param RaceEventDoing[] $doing
     *
     * @return self
     */
    public function setDoing(array $doing): self
    {
        $this->doing = $doing;

        return $this;
    }

    /**
     * @return float|null
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param float|null $rating
     * @return RaceEvent
     */
    public function setRating($rating): self
    {
        $this->rating = $rating;

        return $this;
    }
}
