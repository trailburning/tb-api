<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;

class BaseRepository extends EntityRepository
{
    public function add($entity)
    {
        $this->getEntityManager()->persist($entity);
    }

    public function remove($entity)
    {
        $this->getEntityManager()->remove($entity);
    }

    public function store()
    {
        $this->getEntityManager()->flush();
    }

    public function beginnTransaction()
    {
        $this->getEntityManager()->getConnection()->beginTransaction();
    }

    public function commit()
    {
        $this->getEntityManager()->getConnection()->commit();
    }

    public function rollback()
    {
        $this->getEntityManager()->getConnection()->rollback();
    }

    /**
     * @return QueryBuilder
     */
    public function getQB()
    {
        return $this->createQueryBuilder($this->getRepositoryAlias());
    }

    /**
     * @return string
     *
     * @throws \Exception
     */
    protected function getRepositoryAlias()
    {
        $result = preg_match('@\\\\([\w]+)$@', $this->getEntityName(), $matches);
        if ($result !== 1) {
            throw new \Exception('Unable to extract object name from FQDN: '.$this->getEntityName());
        }
        $objectName = $matches[1];
        $alias = strtolower(substr($objectName, 0, 1));

        return $alias;
    }

    /**
     * @param $entity
     */
    public function refresh($entity)
    {
        $this->getEntityManager()->refresh($entity);
    }
}
