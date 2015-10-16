<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class MediaType extends AbstractEnumType
{
    const IMAGE = 'image';
    
    protected static $choices = [
        self::IMAGE => 'image',
    ];
}
