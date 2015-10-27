<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Swagger\Annotations as SWG;

/**
 * MediaAttribute.
 *
 * @ORM\Table(name="api_media_attribues")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\MediaAttributeRepository")
 */
class MediaAttribute
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
     *
     * @ORM\Column(type="string", length=255)
     */
    private $key;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255)
     */
    private $value;
    
    /**
     * @var Media
     *
     * @ORM\ManyToOne(targetEntity="Media", inversedBy="attributes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $media;

    /**
     * ################################################################################################################
     *
     *                                         User Defined
     *
     * ################################################################################################################
     */
    
    public function __construct($key, $value) {
        $this->setKey($key);
        $this->setValue($value);
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
     * @param string $key
     *
     * @return self
     */
    public function setKey($key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param string $value
     *
     * @return self
     */
    public function setValue($value)
    {
        $this->value = $value;

        return $this;
    }

    /**
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
    
    /**
     * @param Media $media
     * @return self
     */
    public function setmedia(Media $media)
    {
        $this->media = $media;
    
        return $this;
    }

    /**
     * @return Media
     */
    public function getMedia()
    {
        return $this->media;
    }
}
