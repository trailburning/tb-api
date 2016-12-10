<?php

namespace AppBundle\Services;

use AppBundle\DBAL\Types\MIMEType;
use AppBundle\Entity\Media;
use AppBundle\Entity\MediaAttribute;
use AppBundle\Repository\MediaAttributeRepository;
use AppBundle\Repository\MediaRepository;
use Gaufrette\Filesystem;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpKernel\KernelInterface;
use AppBundle\Model\APIResponse;
use Gaufrette\Adapter\MetadataSupporter;

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
     * @param string                   $directory
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

    public function createOrUpdateMedia(FormInterface $form, Media $media = null)
    {
        if ($media === null) {
            $media = new Media();
        }

        $data = $form->getData();
        if ($data['media'] !== null) {
            $media = $this->uploadMedia($data['media'], $media);
        }
        if ($data['credit'] !== null) {
            $media->setCredit($data['credit']);
        }
        if ($data['creditUrl'] !== null) {
            $media->setCreditUrl($data['creditUrl']);
        }
        if ($data['publish'] !== null) {
            $media->setPublish($data['publish']);
        }

        return $media;
    }

    /**
     * @param UploadedFile $file
     * @param Media        $media
     *
     * @return mixed
     *
     * @throws \Exception
     */
    public function uploadMedia(UploadedFile $file, Media $media)
    {
        $mimeType = $this->mediaAnalyzer->getMIMEType($file);
        $filepath = $this->uploadFile($file);
        if ($filepath === false) {
            throw new \Exception('Unable to upload file');
        }
        $path = $this->getAbsoluteFilepath($filepath, $mimeType);
        $media->setMimeType($mimeType);
        $media->setPath($path);
        $media->setAttributes($this->createMediaAttributes($file));
        $this->createShareImage($media, $filepath);

        return $media;
    }

    /**
     * @param Media $media
     * @param $filepath
     */
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
     * @param string $id
     *
     * @return APIResponse
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
     * @param UploadedFile $file The file to upload
     *
     * @return mixed the name of the uploaded file, or fals if upload fails
     */
    public function uploadFile(UploadedFile $file)
    {
        $filepath = $this->generateRelativeFilepath($file);

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
     * @return string
     */
    protected function generateRelativeFilepath(UploadedFile $file)
    {
        $filename = str_replace('.', '', uniqid(null, true));
        $extension = $file->getClientOriginalExtension();
        $filepath = sprintf('%s/%s.%s', $this->directory, $filename, $extension);

        return $filepath;
    }

    /**
     * @param $filepath
     * @param $mimeType
     *
     * @return string
     *
     * @throws \Exception
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
     * @param UploadedFile $file
     *
     * @return array
     */
    private function createMediaAttributes(UploadedFile $file)
    {
        $attributes = [];
        $metadata = $this->mediaAnalyzer->readMetadata($file);
        foreach ($metadata as $key => $value) {
            $attributes[] = new MediaAttribute($key, $value);
        }

        return $attributes;
    }
}
