<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class RaceType extends AbstractEnumType
{
    const ROAD_RUN = 'road_run';
    const TRAIL_RUN = 'trail_run';
    
    protected static $choices = [
        self::ROAD_RUN => 'road_run',
        self::TRAIL_RUN => 'trail_run',
    ];
}
