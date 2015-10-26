<?php

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\File\File;
use Gaufrette\Filesystem;
use AppBundle\DBAL\Types\MIMEType;
use AppBundle\Entity\Media;
use AppBundle\Entity\Asset;
use AppBundle\Repository\MediaRepository;
use AppBundle\Response\APIResponseBuilder;

/**
 * Class MediaService.
 */
class MediaService
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @var MediaRepository
     */
    protected $mediaRepository;
    
    /**
     * @var APIResponseBuilder
     */
    protected $apiResponseBuilder;

    private $mimeTypeDirectoryMap = [
        MIMEType::JPEG => 'images',
        MIMEType::MP3 => 'audio',
        MIMEType::MP4 => 'video',
    ];
    
    private $mimeTypeHostMap = [
        MIMEType::JPEG => 'tbmedia2.imgix.net',
        MIMEType::MP3 => 'media.trailburning.com',
        MIMEType::MP4 => 'media.trailburning.com',
    ];

    /**
     * @param Filesystem      $filesystem
     * @param MediaRepository $mediaRepository
     * @param APIResponseBuilder $apiResponseBuilder
     */
    public function __construct(Filesystem $filesystem, MediaRepository $mediaRepository, APIResponseBuilder $apiResponseBuilder)
    {
        $this->filesystem = $filesystem;
        $this->mediaRepository = $mediaRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
    }

    /**
     * @param array $files
     * @param Asset $Asset
     *
     * @return APIResponse
     */
    public function uploadMedia(array $files, Asset $asset)
    {
        $medias = [];
        foreach ($files as $file) {
            $mimeType = $this->getMIMEType($file);
            $filepath = $this->uploadFile($file);
            $path = $this->getAbsoluteFilepath($filepath, $mimeType);
            $media = new Media();
            $media->setMimeType($mimeType);
            $media->setPath($path);
            $media->setAsset($asset);
            
            $this->mediaRepository->add($media);
            $medias[] = $media;
        }
        $this->mediaRepository->store($media);
        
        return $this->apiResponseBuilder->buildSuccessResponse($medias, 'media', 201);
    }

    /**
     * Upload a Media file to the provided Filesystem
     * Sets the path 45689 filename field.
     *
     * @param File $file The file to upload
     *
     * @return string the name of the uploaded file
     */
    public function uploadFile(File $file)
    {
        $filepath = $this->generateRelativeFilepath($file);

        $adapter = $this->filesystem->getAdapter();
        // Store Metadata to S3 (doesn't work in unit tests when using memory filesystem)
        if ($adapter instanceof \Gaufrette\Adapter\MetadataSupporter) {
            $adapter->setMetadata($filepath, array('ContentType' => 'image/jpeg', 'ACL' => 'public-read'));
        }

        $adapter->write($filepath, file_get_contents($file->getPathname()));

        return $filepath;
    }

    /**
     * @param File $file
     *
     * @return string
     */
    protected function generateRelativeFilepath(File $file)
    {
        $mimeType = $this->getMIMEType($file);
        if (!isset($this->mimeTypeDirectoryMap[$mimeType])) {
            throw new \Exception(sprintf('Unsupported MIME Type: %s', $mimeType));
        }

        $directory = $this->mimeTypeDirectoryMap[$mimeType];
        $filename = str_replace('.', '', uniqid(null, true));
        $extension = $file->getClientOriginalExtension();
        $filepath = sprintf('/%s/%s.%s', $directory, $filename, $extension);

        return $filepath;
    }
    
    /**
     * @param File $file
     *
     * @return string
     */
    protected function getAbsoluteFilepath($filepath, $mimeType)
    {
        if (!isset($this->mimeTypeDirectoryMap[$mimeType])) {
            throw new \Exception(sprintf('Unsupported MIME Type: %s', $mimeType));
        }
        $absoluteFilepath = sprintf('http://%s%s', $this->mimeTypeDirectoryMap[$mimeType], $filepath);
        
        return $absoluteFilepath;
    }

    /**
     * @param File $file
     *
     * @return string
     */
    protected function getMIMEType(File $file)
    {
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $file->getPathname());
        finfo_close($finfo);

        return $mimeType;
    }

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }
}
