<?php

namespace AppBundle\Repository;

use AppBundle\Entity\RaceEvent;
use AppBundle\Entity\RaceEventCompleted;
use AppBundle\Entity\User;

/**
 * RaceEventCompletedRepository.
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class RaceEventCompletedRepository extends BaseRepository
{
    /**
     * @param RaceEvent $raceEvent
     * @param User      $user
     *
     * @return RaceEventCompleted|null
     */
    public function findByRaceEventAndUser(RaceEvent $raceEvent, User $user)
    {
        $qb = $this->getQB()
            ->andWhere('r.raceEvent = :raceEvent')
            ->andWhere('r.user= :user')
            ->setParameter('raceEvent', $raceEvent)
            ->setParameter('user', $user);

        $raceEventCompleted = $qb->getQuery()->getOneOrNullResult();

        return $raceEventCompleted;
    }

    /**
     * @param RaceEvent $raceEvent
     * @return float|null
     */
    public function calculateAvgRatingByRaceEvent(RaceEvent $raceEvent)
    {
        $qb = $this->getQB()
            ->select('avg(r.rating)')
            ->where('r.raceEvent = :raceEvent')
            ->setParameter('raceEvent', $raceEvent)
            ->getQuery();
        $rating= $qb->getSingleResult()[1];

        return $rating;
    }
}
