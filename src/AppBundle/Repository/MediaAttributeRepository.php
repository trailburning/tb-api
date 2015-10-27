<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Media;

class MediaAttributeRepository extends BaseRepository
{
    
    public function deleteByMedia(Media $media) 
    {
        $qb = $this
            ->createQueryBuilder('m')
            ->delete('AppBundle:MediaAttribute', 'm')    
            ->where('m.media = :media')
            ->setParameter(':media', $media);

        return $qb->getQuery()->execute();
    }
    
}
