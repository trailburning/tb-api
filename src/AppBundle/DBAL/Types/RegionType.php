<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class RegionType extends AbstractEnumType
{
    const COUNTRY = 'country';
    const REGION = 'region';
    const PLACE = 'place';
    
    protected static $choices = [
        self::COUNTRY => 'Country',
        self::REGION => 'Region',
        self::PLACE => 'Place',
    ];
}
