<?php

namespace AppBundle\Services;

use Gaufrette\Adapter\MetadataSupporter;
use Gaufrette\Filesystem;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class FileUploader.
 */
class FileUploader
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * FileUploader constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * Upload a Media file to the provided Filesystem
     * Sets the path 45689 filename field.
     *
     * @param UploadedFile $file The file to upload
     * @param null $directory
     *
     * @return mixed the name of the uploaded file, or false if upload fails
     */
    public function upload(UploadedFile $file, $directory = null)
    {
        $filepath = $this->generateRelativeFilepath($file, $directory);

        $adapter = $this->filesystem->getAdapter();
        // Store Metadata to S3 (doesn't work in unit tests when using memory filesystem)
        if ($adapter instanceof MetadataSupporter) {
            $adapter->setMetadata($filepath, array('ContentType' => 'image/jpeg', 'ACL' => 'public-read'));
        }

        $result = $adapter->write($filepath, file_get_contents($file->getPathname()));
        if ($result === false) {
            return false;
        }

        return $filepath;
    }

    /**
     * @param UploadedFile $file
     *
     * @param null $directory
     * @return string
     */
    protected function generateRelativeFilepath(UploadedFile $file, $directory = null): string
    {
        $filename = str_replace('.', '', uniqid(null, true));
        $extension = $file->getClientOriginalExtension();
        if (strpos($extension, '?') !== false) {
            $extension = substr($extension, 0, strpos($extension, '?'));
        }
        $filepath = $filename.'.'.$extension;
        if ($directory !== null) {
            $filepath = $directory.'/'.$filepath;
        }

        return $filepath;
    }
}
