<?php 

namespace AppBundle\Repository\Traits;

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