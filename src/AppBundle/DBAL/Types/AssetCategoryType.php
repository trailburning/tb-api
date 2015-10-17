<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class AssetCategoryType extends AbstractEnumType
{
    const EXPEDITION = 'expedition';
    const FLORA = 'flora';
    const FAUNA = 'fauna';
    const MOUNTAIN = 'mountain';
    const TIME_CAPSULE = 'time_capsule';

    protected static $choices = [
        self::EXPEDITION => 'Expedition',
        self::FLORA => 'Flora',
        self::MOUNTAIN => 'Mountain',
        self::TIME_CAPSULE => 'Time Capsule',
    ];
}
