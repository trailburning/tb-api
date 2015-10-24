<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Journey;

class EventRepository extends BaseRepository
{
    use Traits\Sortable;

    /**
     * @param Journey      $journey
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    public function findByJourneyQB(Journey $journey, QueryBuilder $qb = null)
    {
        if ($qb === null) {
            $qb = $this->getQB();
        }

        $qb->andWhere('e.journey = :journey')->setParameter('journey', $journey);

        return $qb;
    }
}
