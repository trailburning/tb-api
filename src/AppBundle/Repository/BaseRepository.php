<?php

namespace AppBundle\Repository;

use Gedmo\Sortable\Entity\Repository\SortableRepository;

class BaseRepository extends SortableRepository
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
