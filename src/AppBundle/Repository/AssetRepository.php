<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Event;

class AssetRepository extends BaseRepository
{
    use Traits\Sortable;

    /**
     * @param Event        $event
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    public function findByEventQB(Event $event, QueryBuilder $qb = null)
    {
        if ($qb === null) {
            $qb = $this->getQB();
        }

        $qb->andWhere('a.event = :event')->setParameter('event', $event);

        return $qb;
    }
}
