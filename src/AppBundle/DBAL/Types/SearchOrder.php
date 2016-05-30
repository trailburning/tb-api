<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class SearchOrder extends AbstractEnumType
{
    const ASC = 'asc';
    const DESC = 'desc';

    protected static $choices = [
        self::ASC => self::ASC,
        self::DESC => self::DESC,
    ];
}
