<?php 

namespace AppBundle\Services\MetadataReader;

use Symfony\Component\HttpFoundation\File\File;

class MPEGMetadataReader implements MetadataReader
{
    /**
     * @param File $file 
     * @return array
     */
    public function read(File $file) 
    {
        return [];
    }   
}