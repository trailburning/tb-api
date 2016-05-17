<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class RaceDistance extends AbstractEnumType
{
    const MARATHON = 'marathon';
    
    protected static $choices = [
        self::MARATHON => self::MARATHON,
    ];
}
