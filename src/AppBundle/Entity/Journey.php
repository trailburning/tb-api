<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation\Exclude;
use Swagger\Annotations as SWG;

/**
 * Journey.
 *
 * @ORM\Table(name="api_journey")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\JourneyRepository")
 * @SWG\Definition(required={"name", "about"}, @SWG\Xml(name="Journey"))
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
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     * @SWG\Property()
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="text")
     * @SWG\Property()
     */
    private $about;
    
    /**
     * @var boolean
     *
     * @ORM\Column(name="publish", type="boolean", options={"default" = false})
     * @Exclude
     */
    private $publish = false;

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
    public function getPublish()
    {
        return $this->publish;
    }
}
