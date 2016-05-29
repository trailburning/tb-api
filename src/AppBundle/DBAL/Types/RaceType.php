<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class RaceType extends AbstractEnumType
{
    const ROAD = 'road';
    
    protected static $choices = [
        self::ROAD => 'Road',
    ];
}
