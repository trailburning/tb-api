<?php


namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\File\File;
use AppBundle\Services\MetadataReader\MetadataReader;

class MediaAnalyzer
{
    /**
     * @var array
     */
    private $metadataReaders;

    /**
     * @param array $reader
     */
    public function __construct(array $metadataReaders)
    {
        foreach ($metadataReaders as $mimeType => $reader) {
            if (!$reader instanceof MetadataReader) {
                throw new \Exception('Reader does not implement interface MetadataReader: '.get_class($reader));
            }
            $this->metadataReaders[$mimeType] = $reader;
        }
    }

    /**
     * @param File $file
     *
     * @return array
     */
    public function readMetadata(File $file)
    {
        $mimeType = $this->getMIMEType($file);
        if (!isset($this->metadataReaders[$mimeType])) {
            throw new \Exception('No metadata reader found for file of type: ' . $mimeType);
        }
        
        $metadata = $this->metadataReaders[$mimeType]->read($file);
        
        return $metadata;
    }

    /**
     * @param File $file
     *
     * @return string
     */
    public function getMIMEType(File $file)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file->getPathname());
        finfo_close($finfo);

        return $mimeType;
    }
}
