<?php 

namespace AppBundle\Services\MetadataReader;

use Symfony\Component\HttpFoundation\File\File;

class MP3MetadataReader implements MetadataReader
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