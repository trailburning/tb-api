<?php 

namespace AppBundle\Repository\Traits;

use Doctrine\ORM\QueryBuilder;

trait Sortable
{
    /**
     * @param QueryBuilder $qb
     *
     * @return QueryBuilder
     */
    public function addOrderByPositionQB($qb) 
    {
        $qb->addOrderBy($qb->getRootAlias() . '.position');
        
        return $qb;
    }
} 