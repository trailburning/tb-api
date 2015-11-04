<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Event;

class EventCustomRepository extends BaseRepository
{
    
    public function deleteByEvent(Event $event) 
    {
        $qb = $this
            ->createQueryBuilder('e')
            ->delete('AppBundle:EventCustom', 'e')    
            ->where('e.event = :event')
            ->setParameter(':event', $event);

        return $qb->getQuery()->execute();
    }
    
}
