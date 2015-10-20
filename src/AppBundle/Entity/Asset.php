<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;

/**
 * Asset.
 *
 * @ORM\Table(name="api_asset")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssetRepository")
 * @SWG\Definition(required={"id", "about", "category"}, @SWG\Xml(name="Asset"))
 * @Serializer\ExclusionPolicy("all")
 */
class Asset
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     * @SWG\Property(format="int32")
     * @Serializer\Expose
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
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
     * @var Media[]
     *
     * @ORM\OneToMany(targetEntity="Media", mappedBy="asset")
     * @SWG\Property(@SWG\Xml(name="media",wrapped=true))
     * @Serializer\Expose
     */
    protected $medias;
    
    /**
     * @ORM\ManyToOne(targetEntity="Event", inversedBy="assets")
     * @ORM\JoinColumn(nullable=false)
     */
    protected $event;
    
    /**
     * @ORM\ManyToOne(targetEntity="AssetCategory", inversedBy="assets")
     * @Serializer\Expose
     */
    protected $category;

    /**
     * ################################################################################################################
     *
     *                                         User Defined
     *
     * ################################################################################################################
     */


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
     * @param string $category
     * @return self
     */
    public function setCategory($category)
    {
        $this->category = $category;
    
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory()
    {
        return $this->category;
    }
    
    /**
     * @param Media $medias
     * @return User
     */
    public function addMedia(Media $media)
    {
        $this->medias[] = $media;

        return $this;
    }

    /**
     * @param Media $medias
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
     * @param Event $event
     * @return self
     */
    public function setEvent(Event $event)
    {
        $this->event = $event;
    
        return $this;
    }

    /**
     * @return Event
     */
    public function getEvent()
    {
        return $this->event;
    }
}
