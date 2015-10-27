<?php 

namespace AppBundle\Services\MetadataReader;

use Symfony\Component\HttpFoundation\File\File;

interface MetadataReader
{
    /**
     * @param File $file 
     * @return array
     */
    public function read(File $file);
}