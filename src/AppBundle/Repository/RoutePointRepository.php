<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Journey;

class RoutePointRepository extends BaseRepository
{
    
    public function deleteByJourney(Journey $journey) 
    {
        $qb = $this
            ->createQueryBuilder('r')
            ->delete('AppBundle:RoutePoint', 'r')    
            ->where('r.journey = :journey')
            ->setParameter(':journey', $journey);

        return $qb->getQuery()->execute();
    }
    
}
