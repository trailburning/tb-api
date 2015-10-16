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
 * @SWG\Definition(required={"id", "name", "about", "coords"}, @SWG\Xml(name="Journey"))
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
     * @SWG\Property(format="int32")
     * @Serializer\Expose
     */
    private $id;

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
     */
    private $user;
    
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     */
    private $userId;
    
    /**
     * @var Point point
     *
     * @ORM\Column(name="coords", type="point", columnDefinition="GEOMETRY(POINT,4326)")
     */
    private $coords;
    
    /**
      * @ORM\OneToMany(targetEntity="Asset", mappedBy="journey")
      */
    protected $assets;

    /*
     * ################################################################################################################
     *
     *                                         User Defined
     *
     * ################################################################################################################
     */


    /*
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
}
