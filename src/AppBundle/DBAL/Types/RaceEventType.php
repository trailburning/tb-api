<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class RaceEventType extends AbstractEnumType
{
    const TRIATHLON = 'triathlon';
    
    protected static $choices = [
        self::TRIATHLON => 'triathlon',
    ];
}
