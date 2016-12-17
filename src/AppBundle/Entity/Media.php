<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Media.
 *
 * @ORM\Table(name="api_media")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MediaRepository")
 * @SWG\Definition(required={"id", "mimeType", "path"}, @SWG\Xml(name="Media"))
 * @Serializer\ExclusionPolicy("all")
 */
class Media
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
     * @Serializer\Groups({"raceEvent"})
     */
    private $oid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent"})
     */
    private $path;

    /**
     * @var string
     *
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\MIMEType")
     * @ORM\Column(type="MIMEType")
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent"})
     */
    private $mimeType;

    /**
     * @ORM\ManyToOne(targetEntity="Asset", inversedBy="medias")
     */
    protected $asset;

    /**
     * @ORM\ManyToOne(targetEntity="RaceEvent", inversedBy="medias")
     */
    protected $raceEvent;

    /**
     * @var MediaAttribute[]
     *
     * @ORM\OneToMany(targetEntity="MediaAttribute", mappedBy="media", cascade={"persist", "remove"})
     */
    private $attributes;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent"})
     */
    private $credit;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent"})
     */
    private $creditUrl;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent"})
     */
    private $sharePath;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", options={"default" = false})
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent"})
     */
    private $publish = false;

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
        $this->attributes = new ArrayCollection();
    }

    /**
     * @Serializer\VirtualProperty()
     * @Serializer\SerializedName("metadata")
     * @SWG\Property(property="metadata")
     * @Serializer\Groups({"raceEvent"})
     *
     * @return array
     */
    public function getMetadata()
    {
        if (count($this->getAttributes()) === 0) {
            return null;
        }

        $fields = [];
        foreach ($this->getAttributes() as $attribute) {
            $fields[$attribute->getKey()] = $attribute->getValue();
        }

        return $fields;
    }

    /**
     * @return int
     */

    /**
     * ################################################################################################################.
     *
     *                                         Getters and Setters
     *
     * ################################################################################################################
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
     * @param string $path
     *
     * @return self
     */
    public function setPath($path)
    {
        $this->path = $path;

        return $this;
    }

    /**
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }

    /**
     * @param string $mimeType
     *
     * @return self
     */
    public function setMimeType($mimeType)
    {
        $this->mimeType = $mimeType;

        return $this;
    }

    /**
     * @return string
     */
    public function getMimeType()
    {
        return $this->mimeType;
    }

    /**
     * @param Asset $asset
     *
     * @return self
     */
    public function setAsset(Asset $asset)
    {
        $this->asset = $asset;

        return $this;
    }

    /**
     * @return Asset
     */
    public function getAsset()
    {
        return $this->asset;
    }

    /**
     * @param RaceEvent $raceEvent
     *
     * @return Media
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

    /**
     * @param MediaAttribute $attribute
     *
     * @return self
     */
    public function addAttribute(MediaAttribute $attribute)
    {
        $attribute->setMedia($this);
        $this->attributes[] = $attribute;

        return $this;
    }

    /**
     * @param MediaAttribute $attribute
     */
    public function removeAttribute(MediaAttribute $attribute)
    {
        $this->attributes->removeElement($attribute);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAttributes()
    {
        return $this->attributes;
    }

    /**
     * @param array $attributes
     *
     * @return Media
     */
    public function setAttributes(array $attributes)
    {
        foreach ($attributes as $attribute) {
            $this->addAttribute($attribute);
        }

        return $this;
    }

    /**
     * @param string $sharePath
     *
     * @return self
     */
    public function setSharePath($sharePath)
    {
        $this->sharePath = $sharePath;

        return $this;
    }

    /**
     * @return string
     */
    public function getSharePath()
    {
        return $this->sharePath;
    }

    /**
     * @param string $credit
     *
     * @return self
     */
    public function setCredit($credit)
    {
        $this->credit = $credit;

        return $this;
    }

    /**
     * @return string
     */
    public function getCredit()
    {
        return $this->credit;
    }

    /**
     * @param string $creditUrl
     *
     * @return self
     */
    public function setCreditUrl($creditUrl)
    {
        $this->creditUrl = $creditUrl;

        return $this;
    }

    /**
     * @return string
     */
    public function getCreditUrl()
    {
        return $this->creditUrl;
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
}
