<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class AssetCategoryType extends AbstractEnumType
{
    const EXPEDITION = 'Expedition';
    const FLORA = 'Flora';
    const FAUNA = 'Fauna';
    const MOUNTAIN = 'Mountain';
    const TIME_CAPSULE = 'Time Capsule';

    protected static $choices = [
        self::EXPEDITION => 'Expedition',
        self::FLORA => 'Flora',
        self::MOUNTAIN => 'Mountain',
        self::TIME_CAPSULE => 'Time Capsule',
    ];
}
