<?php

namespace AppBundle\Services;

use Symfony\Component\HttpFoundation\File\File;
use Gaufrette\Filesystem;
use AppBundle\DBAL\Types\MIMEType;
use AppBundle\Entity\Media;
use AppBundle\Entity\MediaAttribute;
use AppBundle\Entity\Asset;
use AppBundle\Repository\MediaRepository;
use AppBundle\Repository\AssetRepository;
use AppBundle\Response\APIResponseBuilder;
use AppBundle\Repository\MediaAttributeRepository;

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

    /**
     * @var AssetRepository
     */
    protected $assetRepository;

    /**
     * @var MediaAnalyzer
     */
    protected $mediaAnalyzer;

    /**
     * @var MediaAttributeRepository
     */
    protected $mediaAttributeRepository;

    private $mimeTypeHostMap = [
        MIMEType::JPEG => 'tbmedia2.imgix.net',
        MIMEType::MP3 => 'media.trailburning.com',
        MIMEType::MP4 => 'media.trailburning.com',
    ];

    /**
     * @param Filesystem               $filesystem
     * @param MediaRepository          $mediaRepository
     * @param APIResponseBuilder       $apiResponseBuilder
     * @param MediaAnalyzer            $mediaAnalyzer
     * @param MediaAttributeRepository $mediaAttributeRepository
     */
    public function __construct(
        Filesystem $filesystem,
        MediaRepository $mediaRepository,
        APIResponseBuilder $apiResponseBuilder,
        AssetRepository $assetRepository,
        MediaAnalyzer $mediaAnalyzer,
        MediaAttributeRepository $mediaAttributeRepository)
    {
        $this->filesystem = $filesystem;
        $this->mediaRepository = $mediaRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->assetRepository = $assetRepository;
        $this->mediaAnalyzer = $mediaAnalyzer;
        $this->mediaAttributeRepository = $mediaAttributeRepository;
    }

    /**
     * @param array $files
     * @param Asset $Asset
     *
     * @return APIResponse
     */
    public function createMedia(array $files, Asset $asset)
    {
        $medias = [];
        foreach ($files as $file) {
            $mimeType = $this->mediaAnalyzer->getMIMEType($file);
            $filepath = $this->uploadFile($file);
            if ($filepath === false) {
                return $this->apiResponseBuilder->buildServerErrorResponse();
            }
            $path = $this->getAbsoluteFilepath($filepath, $mimeType);
            $media = new Media();
            $media->setMimeType($mimeType);
            $media->setPath($path);
            $media->setAsset($asset);
            $media->setAttributes($this->createMediaAttributes($file));

            $this->mediaRepository->add($media);
            $medias[] = $media;
        }
        $this->mediaRepository->store();

        return $this->apiResponseBuilder->buildSuccessResponse($medias, 'media', 201);
    }

    /**
     * @param array $files
     * @param Asset $Asset
     *
     * @return APIResponse
     */
    public function updateMedia(File $file, Media $media)
    {
        $mimeType = $this->mediaAnalyzer->getMIMEType($file);
        $filepath = $this->uploadFile($file);
        $path = $this->getAbsoluteFilepath($filepath, $mimeType);
        $media->setMimeType($mimeType);
        $media->setPath($path);

        $this->mediaAttributeRepository->deleteByMedia($media);
        $media->setAttributes($this->createMediaAttributes($file));

        $this->mediaRepository->add($media);
        $this->mediaRepository->store();

        return $this->apiResponseBuilder->buildSuccessResponse([$media], 'media', 200);
    }

    public function deleteMedia($mediaId, $assetId)
    {
        $asset = $this->assetRepository->findOneBy([
            'oid' => $assetId,
        ]);
        if ($asset === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Asset not found');
        }

        $media = $this->mediaRepository->findOneBy([
            'oid' => $mediaId,
        ]);
        if ($media === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Media not found');
        }

        $this->mediaRepository->remove($media);
        $this->mediaRepository->store();

        return $this->apiResponseBuilder->buildEmptySuccessResponse();
    }

    /**
     * Upload a Media file to the provided Filesystem
     * Sets the path 45689 filename field.
     *
     * @param File $file The file to upload
     *
     * @return mixed the name of the uploaded file, or fals if upload fails
     */
    public function uploadFile(File $file)
    {
        $filepath = $this->generateRelativeFilepath($file);

        $adapter = $this->filesystem->getAdapter();
        // Store Metadata to S3 (doesn't work in unit tests when using memory filesystem)
        if ($adapter instanceof \Gaufrette\Adapter\MetadataSupporter) {
            $adapter->setMetadata($filepath, array('ContentType' => 'image/jpeg', 'ACL' => 'public-read'));
        }

        $result = $adapter->write($filepath, file_get_contents($file->getPathname()));
        if ($result === false) {
            return false;
        }

        return $filepath;
    }

    /**
     * @param File $file
     *
     * @return string
     */
    protected function generateRelativeFilepath(File $file)
    {
        $directory = 'test';
        $filename = str_replace('.', '', uniqid(null, true));
        $extension = $file->getClientOriginalExtension();
        $filepath = sprintf('%s/%s.%s', $directory, $filename, $extension);

        return $filepath;
    }

    /**
     * @param File $file
     *
     * @return string
     */
    protected function getAbsoluteFilepath($filepath, $mimeType)
    {
        if (!isset($this->mimeTypeHostMap[$mimeType])) {
            throw new \Exception(sprintf('Unsupported MIME Type: %s', $mimeType));
        }
        $absoluteFilepath = sprintf('http://%s/%s', $this->mimeTypeHostMap[$mimeType], $filepath);

        return $absoluteFilepath;
    }

    /**
     * @param Filesystem $filesystem
     */
    public function setFilesystem(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param File $file
     *
     * @return array
     */
    private function createMediaAttributes(File $file)
    {
        $attributes = [];
        $metadata = $this->mediaAnalyzer->readMetadata($file);
        foreach ($metadata as $key => $value) {
            $attributes[] = new MediaAttribute($key, $value);
        }

        return $attributes;
    }
}
