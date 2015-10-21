<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

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
}
