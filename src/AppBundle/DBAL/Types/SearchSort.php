<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class SearchSort extends AbstractEnumType
{
    const RELEVANCE = 'relevance';
    const DISTANCE = 'distance';
    const DATE = 'date';

    protected static $choices = [
        self::RELEVANCE => self::RELEVANCE,
        self::DISTANCE => self::DISTANCE,
        self::DATE => self::DATE,
    ];
}
