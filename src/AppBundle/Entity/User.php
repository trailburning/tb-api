<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * User
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @ORM\Table(name="fos_user")
 */
class User extends BaseUser
{

    const GENDER_NONE = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;
    
    /**
     * @var integer
     *
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    protected $id;
    
    /**
     * @var string
     *
     * @Gedmo\Slug(fields={"firstName", "lastName"}, updatable=false, separator="")
     * @ORM\Column(type="string", length=50, nullable=true, unique=true)
     */
    protected $name;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $displayName;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     */
    private $lastName;

    /**
     * @var Point
     *
     * @ORM\Column(type="point", columnDefinition="GEOMETRY(POINT,4326)")
     * @Assert\NotBlank()
     */
    private $location; 
    
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $about;
    
    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     */
    private $synopsis;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $avatar;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $avatarGravatar;
    
    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $activityUnseenCount;
    
    /**
     * @var datetime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $activityLastViewed;
    
    /**
     * @var integer
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $homepageOrder;
    
    /**
     * @var integer
     *
     * @ORM\Column(type="smallint")
     */
    private $gender = 0;    
    
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"default" = true})
     */
    private $newsletter = true;
    
    /**
     * @var datetime
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime")
     */
    protected $registeredAt;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $oauthService;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $oauthId;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $oauthAccessToken;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $avatarFacebook;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string")
     */
    private $discr = "UserProfile";
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $abstract;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $subtitle;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $headerImage;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $logo;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $link; 
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    private $shareImage;
    
    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", options={"default" = false}, nullable=true)
     */
    private $isAmbassador = false;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $ambassadorTagline;
    
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50, nullable=true)
     */
    private $ambassadorLocation;
    
    /**
     * @var \Doctrine\Common\Collections\Collection
     *
     * @ORM\OneToMany(targetEntity="Journey", mappedBy="user")
     **/
    private $journeys;

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
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * @param string $about
     * @return User
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
     * @param string $synopsis
     * @return User
     */
    public function setSynopsis($synopsis)
    {
        $this->synopsis = $synopsis;

        return $this;
    }

    /**
     * @return string 
     */
    public function getSynopsis()
    {
        return $this->synopsis;
    }

    /**
     * @param string $avatar
     * @return User
     */
    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;

        return $this;
    }

    /**
     * @return string 
     */
    public function getAvatar()
    {
        return $this->avatar;
    }
    
    /**
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->setUsername($email);

        return parent::setEmail($email);
    }

    /**
     * @param string $emailCanonical
     * @return User
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->setUsernameCanonical($emailCanonical);

        return parent::setEmailCanonical($emailCanonical);
    }
    
    /**
     * @param string $name
     * @return User
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
     * @param string $lastName
     * @return self
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * @return string 
     */
    public function getLastName()
    {
        return $this->lastName;
    }

    /**
     * @param point $location
     * @return self
     */
    public function setLocation($location)
    {
        if (is_string($location)) {
            // check the location Sting format
            if (!preg_match('/^\(([-\d]+\.[-\d]+), ([-\d]+\.[-\d]+)\)$/', $location, $match)) {
                throw new \Exception(sprintf('Invalid location string format: %s', $location));
            }
            $location = new Point($match[1], $match[2], 4326);
        }
        
        $this->location = $location;

        return $this;
    }

    /**
     * @return point 
     */
    public function getLocation()
    {
        return $this->location;
    }

    /**
     * @param string $firstName
     * @return self
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    /**
     * @return string 
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * @param string $avatarGravatar
     * @return User
     */
    public function setAvatarGravatar($avatarGravatar)
    {
        $this->avatarGravatar = $avatarGravatar;

        return $this;
    }

    /**
     * @return string 
     */
    public function getAvatarGravatar()
    {
        return $this->avatarGravatar;
    }
    
    public function setHomepageOrder($homepageOrder)
    {
        $this->homepageOrder = $homepageOrder;

        return $this;
    }

    /**
     * @return integer 
     */
    public function getHomepageOrder()
    {
        return $this->homepageOrder;
    }

    /**
     * @param integer $gender
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return integer 
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param boolean $newsletter
     * @return User
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * @return boolean 
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * @param \DateTime $registeredAt
     * @return User
     */
    public function setRegisteredAt($registeredAt)
    {
        $this->registeredAt = $registeredAt;

        return $this;
    }

    /**
     * @return \DateTime 
     */
    public function getRegisteredAt()
    {
        return $this->registeredAt;
    }
    
    /**
     * @param string $oauthService
     * @return User
     */
    public function setOAuthService($oauthService)
    {
        $this->oAuthService = $oauthService;

        return $this;
    }

    /**
     * @return string 
     */
    public function getOAuthService()
    {
        return $this->oAuthService;
    }

    /**
     * @param string $oauthId
     * @return User
     */
    public function setOAuthId($oauthId)
    {
        $this->oAuthId = $oauthId;

        return $this;
    }

    /**
     * @return string 
     */
    public function getOAuthId()
    {
        return $this->oAuthId;
    }

    /**
     * @param string $oauthAccessToken
     * @return User
     */
    public function setOAuthAccessToken($oauthAccessToken)
    {
        $this->oAuthAccessToken = $oauthAccessToken;

        return $this;
    }

    /**
     * @return string 
     */
    public function getOAuthAccessToken()
    {
        return $this->oAuthAccessToken;
    }
    
    /**
     * @param string $avatarFacebook
     * @return User
     */
    public function setAvatarFacebook($avatarFacebook)
    {
        $this->avatarFacebook = $avatarFacebook;

        return $this;
    }

    /**
     * @return string 
     */
    public function getAvatarFacebook()
    {
        return $this->avatarFacebook;
    }
    
    /**
     * @param boolean $isAmbassador
     * @return self
     */
    public function setIsAmbassador($isAmbassador)
    {
        $this->isAmbassador = $isAmbassador;

        return $this;
    }

    /**
     * @return boolean 
     */
    public function getIsAmbassador()
    {
        return $this->isAmbassador;
    }

    /**
     * @param string $ambassadorTagline
     * @return self
     */
    public function setAmbassadorTagline($ambassadorTagline)
    {
        $this->ambassadorTagline = $ambassadorTagline;

        return $this;
    }

    /**
     * @return string 
     */
    public function getAmbassadorTagline()
    {
        return $this->ambassadorTagline;
    }
    
    /**
     * @param string $ambassadorLocation
     * @return self
     */
    public function setAmbassadorLocation($ambassadorLocation)
    {
        $this->ambassadorLocation = $ambassadorLocation;

        return $this;
    }

    /**
     * @return string 
     */
    public function getAmbassadorLocation()
    {
        return $this->ambassadorLocation;
    }
    
    /**
     * @param string $headerImage
     * @return self
     */
    public function setHeaderImage($headerImage)
    {
        $this->headerImage = $headerImage;

        return $this;
    }

    /**
     * @return string 
     */
    public function getHeaderImage()
    {
        return $this->headerImage;
    }

    /**
     * @param string $logo
     * @return self
     */
    public function setLogo($logo)
    {
        $this->logo = $logo;

        return $this;
    }

    /**
     * @return string 
     */
    public function getLogo()
    {
        return $this->logo;
    }
    
    /**
     * @param string $displayName
     * @return self
     */
    public function setDisplayName($displayName)
    {
        $this->displayName = $displayName;

        return $this;
    }

    /**
     * @return string 
     */
    public function getDisplayName()
    {
        return $this->displayName;
    }

    /**
     * @param string $abstract
     * @return self
     */
    public function setAbstract($abstract)
    {
        $this->abstract = $abstract;

        return $this;
    }

    /**
     * @return string 
     */
    public function getAbstract()
    {
        return $this->abstract;
    }

    /**
     * @param string $subtitle
     * @return self
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * @return string 
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * @param string $link
     * @return self
     */
    public function setLink($link)
    {
        $this->link = $link;

        return $this;
    }

    /**
     * @return string 
     */
    public function getLink()
    {
        return $this->link;
    }
    
    /**
     * @param Journey $journeys
     * @return User
     */
    public function addJourney(Journey $journeys)
    {
        $this->journeys[] = $journeys;

        return $this;
    }

    /**
     * @param Journey $journeys
     */
    public function removeJourney(Journey $journeys)
    {
        $this->journeys->removeElement($journeys);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getJourneys()
    {
        return $this->journeys;
    }
}
