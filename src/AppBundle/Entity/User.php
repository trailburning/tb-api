<?php

namespace AppBundle\Entity;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;
use Fresh\DoctrineEnumBundle\Validator\Constraints as DoctrineAssert;
use AppBundle\DBAL\Types\UserClientType;
use Swagger\Annotations as SWG;
use JMS\Serializer\Annotation as Serializer;

/**
 * User.
 *
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UserRepository")
 * @SWG\Definition(required={"email", "firstName", "lastName", "gender"}, @SWG\Xml(name="User"))
 * @ORM\Table(name="fos_user")
 * @Serializer\ExclusionPolicy("all")
 */
class User extends BaseUser
{
    const GENDER_NONE = 0;
    const GENDER_MALE = 1;
    const GENDER_FEMALE = 2;

    /**
     * @var int
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
     * @Serializer\Expose
     * @SWG\Property()
     */
    private $firstName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=50)
     * @Assert\NotBlank()
     * @Serializer\Expose
     * @SWG\Property()
     */
    private $lastName;

    /**
     * @var Point
     *
     * @ORM\Column(type="point", columnDefinition="GEOMETRY(POINT,4326)", nullable=true)
     * @Serializer\Expose
     * @SWG\Property()
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
     * @var int
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
     * @var int
     *
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $homepageOrder;

    /**
     * @var int
     *
     * @ORM\Column(type="smallint")
     * @Serializer\Expose
     * @SWG\Property()
     */
    private $gender = 0;

    /**
     * @var bool
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
    private $discr = 'UserProfile';

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
     * @var bool
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
     * @var string
     *
     * @ORM\Column(type="UserClientType", nullable=true)
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\UserClientType")
     */
    private $client;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Serializer\Expose
     * @SWG\Property()
     */
    private $socialMedia;

    /**
     * @var string
     *
     * @DoctrineAssert\Enum(entity="AppBundle\DBAL\Types\RaceEventType")
     * @ORM\Column(type="RaceEventType", nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     */
    private $raceEventType;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @SWG\Property()
     */
    private $raceDistanceMin;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @SWG\Property()
     */
    private $raceDistanceMax;

    /**
     * ################################################################################################################.
     *
     *                                         User Defined
     *
     * ################################################################################################################
     */
    public function __construct()
    {
        $this->client = UserClientType::RACE_BASE;
        parent::__construct();
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
     * @param string $about
     *
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
     *
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
     *
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
     *
     * @return User
     */
    public function setEmail($email)
    {
        $this->setUsername($email);

        return parent::setEmail($email);
    }

    /**
     * @param string $emailCanonical
     *
     * @return User
     */
    public function setEmailCanonical($emailCanonical)
    {
        $this->setUsernameCanonical($emailCanonical);

        return parent::setEmailCanonical($emailCanonical);
    }

    /**
     * @param string $name
     *
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
     *
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
     *
     * @return self
     */
    public function setLocation($location)
    {
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
     *
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
     *
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
     * @return int
     */
    public function getHomepageOrder()
    {
        return $this->homepageOrder;
    }

    /**
     * @param int $gender
     *
     * @return User
     */
    public function setGender($gender)
    {
        $this->gender = $gender;

        return $this;
    }

    /**
     * @return int
     */
    public function getGender()
    {
        return $this->gender;
    }

    /**
     * @param bool $newsletter
     *
     * @return User
     */
    public function setNewsletter($newsletter)
    {
        $this->newsletter = $newsletter;

        return $this;
    }

    /**
     * @return bool
     */
    public function getNewsletter()
    {
        return $this->newsletter;
    }

    /**
     * @param \DateTime $registeredAt
     *
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
     * @param string $avatarFacebook
     *
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
     * @param bool $isAmbassador
     *
     * @return self
     */
    public function setAmbassador($isAmbassador)
    {
        $this->isAmbassador = $isAmbassador;

        return $this;
    }

    /**
     * @return bool
     */
    public function isAmbassador()
    {
        return $this->isAmbassador;
    }

    /**
     * @param string $ambassadorTagline
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
     *
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
    /**
     * @param string $client
     *
     * @return self
     */
    public function setClient($client)
    {
        $this->client = $client;

        return $this;
    }

    /**
     * @return string
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * Set the value of Id.
     *
     * @param int id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of Activity Unseen Count.
     *
     * @return int
     */
    public function getActivityUnseenCount()
    {
        return $this->activityUnseenCount;
    }

    /**
     * Set the value of Activity Unseen Count.
     *
     * @param int activityUnseenCount
     *
     * @return self
     */
    public function setActivityUnseenCount($activityUnseenCount)
    {
        $this->activityUnseenCount = $activityUnseenCount;

        return $this;
    }

    /**
     * Get the value of Activity Last Viewed.
     *
     * @return datetime
     */
    public function getActivityLastViewed()
    {
        return $this->activityLastViewed;
    }

    /**
     * Set the value of Activity Last Viewed.
     *
     * @param datetime activityLastViewed
     *
     * @return self
     */
    public function setActivityLastViewed(\DateTime $activityLastViewed)
    {
        $this->activityLastViewed = $activityLastViewed;

        return $this;
    }

    /**
     * Get the value of Oauth Service.
     *
     * @return string
     */
    public function getOauthService()
    {
        return $this->oauthService;
    }

    /**
     * Set the value of Oauth Service.
     *
     * @param string oauthService
     *
     * @return self
     */
    public function setOauthService($oauthService)
    {
        $this->oauthService = $oauthService;

        return $this;
    }

    /**
     * Get the value of Oauth Id.
     *
     * @return string
     */
    public function getOauthId()
    {
        return $this->oauthId;
    }

    /**
     * Set the value of Oauth Id.
     *
     * @param string oauthId
     *
     * @return self
     */
    public function setOauthId($oauthId)
    {
        $this->oauthId = $oauthId;

        return $this;
    }

    /**
     * Get the value of Oauth Access Token.
     *
     * @return string
     */
    public function getOauthAccessToken()
    {
        return $this->oauthAccessToken;
    }

    /**
     * Set the value of Oauth Access Token.
     *
     * @param string oauthAccessToken
     *
     * @return self
     */
    public function setOauthAccessToken($oauthAccessToken)
    {
        $this->oauthAccessToken = $oauthAccessToken;

        return $this;
    }

    /**
     * Get the value of Discr.
     *
     * @return string
     */
    public function getDiscr()
    {
        return $this->discr;
    }

    /**
     * Set the value of Discr.
     *
     * @param string discr
     *
     * @return self
     */
    public function setDiscr($discr)
    {
        $this->discr = $discr;

        return $this;
    }

    /**
     * Get the value of Share Image.
     *
     * @return string
     */
    public function getShareImage()
    {
        return $this->shareImage;
    }

    /**
     * Set the value of Share Image.
     *
     * @param string shareImage
     *
     * @return self
     */
    public function setShareImage($shareImage)
    {
        $this->shareImage = $shareImage;

        return $this;
    }

    /**
     * Set the value of Journeys.
     *
     * @param mixed journeys
     *
     * @return self
     */
    public function setJourneys($journeys)
    {
        $this->journeys = $journeys;

        return $this;
    }

    /**
     * Get the value of Social Media.
     *
     * @return string
     */
    public function getSocialMedia()
    {
        return $this->socialMedia;
    }

    /**
     * Set the value of Social Media.
     *
     * @param string socialMedia
     *
     * @return self
     */
    public function setSocialMedia($socialMedia)
    {
        $this->socialMedia = $socialMedia;

        return $this;
    }

    /**
     * Get the value of Race Event Type.
     *
     * @return string
     */
    public function getRaceEventType()
    {
        return $this->raceEventType;
    }

    /**
     * Set the value of Race Event Type.
     *
     * @param string raceEventType
     *
     * @return self
     */
    public function setRaceEventType($raceEventType)
    {
        $this->raceEventType = $raceEventType;

        return $this;
    }

    /**
     * Get the value of Race Distance Min.
     *
     * @return int
     */
    public function getRaceDistanceMin()
    {
        return $this->raceDistanceMin;
    }

    /**
     * Set the value of Race Distance Min.
     *
     * @param int raceDistanceMin
     *
     * @return self
     */
    public function setRaceDistanceMin($raceDistanceMin)
    {
        $this->raceDistanceMin = $raceDistanceMin;

        return $this;
    }

    /**
     * Get the value of Race Distance Max.
     *
     * @return int
     */
    public function getRaceDistanceMax()
    {
        return $this->raceDistanceMax;
    }

    /**
     * Set the value of Race Distance Max.
     *
     * @param int RaceDistanceMax
     *
     * @return self
     */
    public function setRaceDistanceMax($raceDistanceMax)
    {
        $this->raceDistanceMax = $raceDistanceMax;

        return $this;
    }
}
