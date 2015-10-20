<?php

namespace AppBundle\DBAL\Types;

use Fresh\DoctrineEnumBundle\DBAL\Types\AbstractEnumType;

class MIMEType extends AbstractEnumType
{
    const JPEG = 'jpeg';
    const MP3 = 'mp3';
    const MP4 = 'mp4';
    
    protected static $choices = [
        self::JPEG => 'image/jpeg',
        self::MP3 => 'audio/mpeg',
        self::MP4 => 'video/mp4',
    ];
}
