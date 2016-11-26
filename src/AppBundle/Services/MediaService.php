<?php

namespace AppBundle\Services;

use AppBundle\DBAL\Types\MIMEType;
use AppBundle\Entity\Asset;
use AppBundle\Entity\RaceEvent;
use AppBundle\Entity\Media;
use AppBundle\Entity\MediaAttribute;
use AppBundle\Repository\MediaAttributeRepository;
use AppBundle\Repository\MediaRepository;
use Gaufrette\Filesystem;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\HttpKernel\KernelInterface;

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
     * @var MediaAnalyzer
     */
    protected $mediaAnalyzer;

    /**
     * @var MediaAttributeRepository
     */
    protected $mediaAttributeRepository;

    /**
     * @var ImageService
     */
    private $imageService;

    /**
     * @var KernelInterface
     */
    private $kernel;

    /**
     * @var
     */
    private $directory;

    private $mimeTypeHostMap = [
        MIMEType::JPEG => 'tbmedia2.imgix.net',
        MIMEType::MP3 => 'media.trailburning.com',
        MIMEType::MP4 => 'media.trailburning.com',
        MIMEType::XM4V => 'media.trailburning.com',
    ];

    /**
     * @param Filesystem               $filesystem
     * @param MediaRepository          $mediaRepository
     * @param APIResponseBuilder       $apiResponseBuilder
     * @param MediaAnalyzer            $mediaAnalyzer
     * @param MediaAttributeRepository $mediaAttributeRepository
     * @param ImageService             $imageService
     * @param KernelInterface          $kernel
     */
    public function __construct(
        Filesystem $filesystem,
        MediaRepository $mediaRepository,
        APIResponseBuilder $apiResponseBuilder,
        MediaAnalyzer $mediaAnalyzer,
        MediaAttributeRepository $mediaAttributeRepository,
        ImageService $imageService,
        KernelInterface $kernel,
        $directory)
    {
        $this->filesystem = $filesystem;
        $this->mediaRepository = $mediaRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->mediaAnalyzer = $mediaAnalyzer;
        $this->mediaAttributeRepository = $mediaAttributeRepository;
        $this->imageService = $imageService;
        $this->kernel = $kernel;
        $this->directory = $directory;
    }

    /**
     * @param array     $files
     * @param Asset     $asset
     * @param RaceEvent $raceEvent
     *
     * @return APIResponse
     */
    public function createMedia(array $files, Asset $asset = null, RaceEvent $raceEvent = null)
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
            if ($asset !== null) {
                $media->setAsset($asset);
            }
            if ($raceEvent !== null) {
                $media->setRaceEvent($raceEvent);
            }
            $media->setAttributes($this->createMediaAttributes($file));
            $this->createShareImage($media, $filepath);

            $this->mediaRepository->add($media);
            $medias[] = $media;
        }
        $this->mediaRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse(201);
    }

    private function createShareImage(Media $media, $filepath)
    {
        if ($media->getMimeType() === MIMEType::JPEG) {
            $pathParts = pathinfo($filepath);
            $shareFilepath = sprintf('%s/%s_share.%s', $pathParts['dirname'], $pathParts['filename'], $pathParts['extension']);
            $watermarkFilepath = $this->kernel->locateResource('@AppBundle/Resources/watermark/share_1200x630.png');
            $this->imageService->createShareImage($filepath, $shareFilepath, $watermarkFilepath, $this->filesystem);
            $sharePath = $this->getAbsoluteFilepath($shareFilepath, $media->getMimeType());
            $media->setSharePath($sharePath);
        }
    }

    /**
     * @param array $files
     * @param Media $media
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
        $this->createShareImage($media, $filepath);

        $this->mediaRepository->add($media);
        $this->mediaRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse(204);
    }

    /**
     * @param string $id
     */
    public function deleteMedia($id)
    {
        $media = $this->mediaRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($media === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Media not found');
        }

        $this->mediaRepository->remove($media);
        $this->mediaRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse(204);
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
        $filename = str_replace('.', '', uniqid(null, true));
        $extension = $file->getClientOriginalExtension();
        $filepath = sprintf('%s/%s.%s', $this->directory, $filename, $extension);

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
