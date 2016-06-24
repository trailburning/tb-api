<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class RaceCategory extends AbstractEnumType
{
    const ULTRA_MARATHON = 'ultra_marathon';
    const MARATHON = 'marathon';
    const HALF_MARATHON = 'half_marathon';
    const FIVE_K = '5k';
    const TEN_K = '10k';
    
    protected static $choices = [
        self::ULTRA_MARATHON => 'Ultra ‚Marathon',
        self::MARATHON => 'Marathon',
        self::HALF_MARATHON => 'Half Marathon',
        self::FIVE_K => '5K',
        self::TEN_K => '10K',
    ];
}
