<?php

namespace AppBundle\Repository;

use AppBundle\Entity\RaceEvent;
use AppBundle\Entity\RaceEventWishlist;
use AppBundle\Entity\User;

/**
 * RaceEventWishlistRepository
 *
 * This class was generated by the PhpStorm "Php Annotations" Plugin. Add your own custom
 * repository methods below.
 */
class RaceEventWishlistRepository extends BaseRepository
{
    /**
     * @param RaceEvent $raceEvent
     * @param User      $user
     *
     * @return RaceEventWishlist|null
     */
    public function findByRaceEventAndUser(RaceEvent $raceEvent, User $user)
    {
        $qb = $this->getQB()
            ->andWhere('r.raceEvent = :raceEvent')
            ->andWhere('r.user= :user')
            ->setParameter('raceEvent', $raceEvent)
            ->setParameter('user', $user);

        $raceEventWishlist = $qb->getQuery()->getOneOrNullResult();

        return $raceEventWishlist;
    }
}
