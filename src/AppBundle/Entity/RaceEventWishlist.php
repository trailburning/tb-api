<?php

namespace AppBundle\Entity;

use Datetime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * RaceEventWishlist.
 *
 * @ORM\Table(name="api_race_event_wishlist",uniqueConstraints={@ORM\UniqueConstraint(name="unique_race_event_user_wishlist", columns={"race_event_id", "user_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceEventWishlistRepository")
 * @SWG\Definition(required={"id"}, @SWG\Xml(name="RaceEventWishlist"))
 * @Serializer\ExclusionPolicy("all")
 */
class RaceEventWishlist
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
     * @var RaceEvent
     *
     * @ORM\ManyToOne(targetEntity="RaceEvent", inversedBy="wishlist")
     * @ORM\JoinColumn(nullable=false)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"user"})
     */
    private $raceEvent;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="wishlistRaceEvents")
     * @ORM\JoinColumn(nullable=false)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $user;

    /**
     * @var Datetime $created
     *
     * @Gedmo\Timestampable(on="create")
     * @ORM\Column(type="datetime", nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $timestamp;

    /**
     * ################################################################################################################.
     *
     *                                         User Defined
     *
     * ################################################################################################################
     */
    public function __construct()
    {
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
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     *
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;

        return $this;
    }

    /**
     * @return RaceEvent
     */
    public function getRaceEvent(): RaceEvent
    {
        return $this->raceEvent;
    }

    /**
     * @param RaceEvent $raceEvent
     *
     * @return self
     */
    public function setRaceEvent(RaceEvent $raceEvent): self
    {
        $this->raceEvent = $raceEvent;

        return $this;
    }

    /**
     * @return User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param User $user
     *
     * @return self
     */
    public function setUser(User $user): self
    {
        $this->user = $user;

        return $this;
    }

    /**
     * @return Datetime
     */
    public function getTimestamp(): Datetime
    {
        return $this->timestamp;
    }
}
