<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class MIMEType extends AbstractEnumType
{
    const JPEG = 'image/jpeg';
    const MP3 = 'audio/mpeg';
    const MP4 = 'video/mp4';
    const XM4V = 'video/x-m4v';

    protected static $choices = [
        self::JPEG => self::JPEG,
        self::MP3 => self::MP3,
        self::MP4 => self::MP4,
        self::XM4V => self::XM4V,
    ];
}
