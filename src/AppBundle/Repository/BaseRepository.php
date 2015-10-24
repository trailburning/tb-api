<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Gedmo\Sortable\Entity\Repository\SortableRepository;

class BaseRepository extends EntityRepository
{    
    
    public function add($entity) 
    {
        $this->getEntityManager()->persist($entity);
    }
    
    public function store() 
    {
        $this->getEntityManager()->flush();
    }
    
    /**
     * @return QueryBuilder
     */
    public function getQB()
    {
        return $this->createQueryBuilder($this->getRepositoryAlias());
    }
    
    protected function getRepositoryAlias() 
    {
        $result = preg_match('@\\\\([\w]+)$@', $this->getEntityName(), $matches);
        if ($result !== 1) {
            throw new \Exception('Unable to extract object name from FQDN: ' . $this->getEntityName());
        }
        $objectName = $matches[1];
        $alias = strtolower(substr($objectName, 0, 1));
        
        return $alias;
    }
}