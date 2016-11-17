<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class UserClientType extends AbstractEnumType
{
    const RACE_BASE = 'race_base';

    protected static $choices = [
        self::RACE_BASE => self::RACE_BASE,
    ];
}
