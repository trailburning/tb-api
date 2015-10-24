<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;

class JourneyRepository extends BaseRepository
{
    use Traits\Sortable;
    
    /**
     * @param User         $user
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    public function findPublishedByUserQB(User $user, QueryBuilder $qb = null)
    {
        if ($qb === null) {
            $qb = $this->getQB();
        }
        
        $qb
            ->andWhere('j.user = :user')->setParameter('user', $user)
            ->andWhere('j.publish = :publish')->setParameter('publish', true);

        return $qb;
    }
}
