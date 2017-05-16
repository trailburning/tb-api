<?php

namespace AppBundle\Repository;

use AppBundle\Entity\User;

class UserRepository extends BaseRepository
{
    /**
     * @param int $limit
     *
     * @return User[]
     */
    public function findLatestActiveWithAvatar($limit = 10): array
    {
        $clientId = 'race_base';
        $qb = $this->getQB();
        $qb->andWhere('u.client = :client_id');
        $qb->setParameter('client_id', $clientId);
        $qb->andWhere('u.enabled = true');
        $qb->addOrderBy('u.registeredAt', 'DESC');
        $qb->setMaxResults($limit);
        $result = $qb->getQuery()->getResult();

        return $result;
    }

    /**
     * @return int
     */
    public function getUserCount()
    {
        $qb = $this->getQB()
            ->select('count(u.id)')
            ->andWhere('u.client = :client')
            ->setParameter('client', 'race_base')
            ->andWhere('u.enabled = true');

        return $qb->getQuery()->getSingleScalarResult();
    }
}
