<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;

/**
 * AssetCategory.
 *
 * @ORM\Table(name="api_asset_category")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\AssetCategoryRepository")
 * @SWG\Definition(required={"name", "label"}, @SWG\Xml(name="AssetCategory"))
 * @Serializer\ExclusionPolicy("all")
 */
class AssetCategory
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
     * @ORM\Column(type="string", length=255, nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     */
    private $name;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     */
    private $label;
    
    /**
     * @var Asset[]
     *
     * @ORM\OneToMany(targetEntity="Asset", mappedBy="category")
     */
    protected $assets;
    
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
     * @param Asset $assets
     * @return User
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
        $this->medias->removeElement($asset);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getAssets()
    {
        return $this->assets;
    }
    
    /**
     * @param string $label
     *
     * @return self
     */
    public function setLabel($label)
    {
        $this->label = $label;

        return $this;
    }

    /**
     * @return string
     */
    public function getLabel()
    {
        return $this->label;
    }
}
