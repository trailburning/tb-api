<?php

namespace AppBundle\Entity;

use Datetime;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as Serializer;
use Swagger\Annotations as SWG;
use Symfony\Component\Validator\Constraints as Assert;
use Gedmo\Mapping\Annotation as Gedmo;

/**
 * RaceEventCompleted.
 *
 * @ORM\Table(name="api_race_event_completed",uniqueConstraints={@ORM\UniqueConstraint(name="unique_race_event_user_completed", columns={"race_event_id", "user_id"})})
 * @ORM\Entity(repositoryClass="AppBundle\Repository\RaceEventCompletedRepository")
 * @SWG\Definition(required={"id"}, @SWG\Xml(name="RaceEventCompleted"))
 * @Serializer\ExclusionPolicy("all")
 */
class RaceEventCompleted
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
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     * @Assert\Range(min=1,max=5)
     */
    private $rating;

    /**
     * @var string
     *
     * @ORM\Column(type="text", nullable=true)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $comment;

    /**
     * @var RaceEvent
     *
     * @ORM\ManyToOne(targetEntity="RaceEvent", inversedBy="completed")
     * @ORM\JoinColumn(nullable=false)
     * @SWG\Property()
     * @Serializer\Expose
     * @Serializer\Groups({"user"})
     */
    private $raceEvent;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="User", inversedBy="completedRaceEvents")
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
     * @return int
     */
    public function getRating()
    {
        return $this->rating;
    }

    /**
     * @param int $rating
     *
     * @return self
     */
    public function setRating(int $rating): self
    {
        $this->rating = $rating;

        return $this;
    }

    /**
     * @return string
     */
    public function getComment()
    {
        return $this->comment;
    }

    /**
     * @param string $comment
     *
     * @return self
     */
    public function setComment(string $comment): self
    {
        $this->comment = $comment;

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
    public function getTimestamp()
    {
        return $this->timestamp;
    }
}
