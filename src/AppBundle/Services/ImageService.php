<?php

namespace AppBundle\Services;

use Gaufrette\Filesystem;

/**
 * 
 */
class ImageService
{
    protected $em;

    /**
     * @var Filesystem
     */
    private $filesystem;

    /**
     * @param Filesystem $filesystem 
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }
    
    /**
     * @param string $imagePath 
     * @param string $shareImagePath 
     * @param string $watermarkPath 
     * @param Filesystem $filesystem 
     * @param string $logoPath 
     * @return bool
     */
    public function createShareImage($imagePath, $shareImagePath, $watermarkPath, Filesystem $filesystem, $logoPath = null)
    {
        $image = imagecreatefromstring($filesystem->read($imagePath));
        if (preg_match('/^\//', $watermarkPath)) {
            // path to watermark is an absolute path, open the file from the filesystem
            $watermark = imagecreatefrompng($watermarkPath);
        } else {
            // the path is points to a file on the gaufrette filesystem, read it from that filesystem
            $watermark = imagecreatefromstring($filesystem->read($watermarkPath));
        }

        $watermarkWidth = imagesx($watermark);
        $watermarkHeight = imagesy($watermark);
        $watermarkRatio = $watermarkWidth / $watermarkHeight;
        $imageWidth = imagesx($image);
        $imageHeight = imagesy($image);

        // Get the ratio of the image, it must be the same than the watermark
        $imageRatio = $imageWidth / $imageHeight;
        if ($imageRatio != $watermarkRatio) {
            // Calculate the new image x and y that conforms to the ratio of the watermark
            if (($imageHeight * $watermarkRatio) > $imageWidth) {
                $newWidth = $imageWidth;
                $newHeight = $imageWidth / $watermarkRatio;
            } else {
                $newWidth = $imageHeight * $watermarkRatio;
                $newHeight = $imageHeight;
            }
            // center the image when cropping
            $newX = ($imageHeight - $newHeight) / 2;
            $newY = ($imageWidth - $newWidth) / 2;

            $newImage = imagecreatetruecolor($newWidth, $newHeight);
            imagecopyresampled($newImage, $image, 0, 0, $newY, $newX, $imageWidth, $imageHeight, $imageWidth, $imageHeight);
            $image = $newImage;
            $imageWidth = $newWidth;
            $imageHeight = $newHeight;
        }

        // Image and watermark template must have the same size, resize either the image or the watermark
        if ($imageWidth > $watermarkWidth) {
            // The image is larger than the watermark, resize the image to the size of the watermark
            $newImage = imagecreatetruecolor($watermarkWidth, $watermarkHeight);
            imagecopyresampled($newImage, $image, 0, 0, 0, 0, $watermarkWidth, $watermarkHeight, $imageWidth, $imageHeight);
            $image = $newImage;
        } elseif ($imageWidth < $watermarkWidth) {
            // The image is smaller than the watermark, resize the watermark to the size of the image
            $newWatermark = imagecreatetruecolor($imageWidth, $imageHeight);
            imagealphablending($newWatermark, false);
            imagesavealpha($newWatermark, true);
            imagecopyresampled($newWatermark, $watermark, 0, 0, 0, 0, $imageWidth, $imageHeight, $watermarkWidth, $watermarkHeight);
            $watermark = $newWatermark;
            $watermarkWidth = $imageWidth;
            $watermarkHeight = $imageHeight;
        }

        // Create the share image
        imagecopy($image, $watermark, imagesx($image) - $watermarkWidth, imagesy($image) - $watermarkHeight, 0, 0, $watermarkWidth, $watermarkHeight);

        if ($logoPath !== null) {
            $logo = imagecreatefromstring($filesystem->read($logoPath));
            $logoWidth = imagesx($logo);
            $logoHeight = imagesy($logo);
            if ($logoWidth < 250) {
                // put logo in the upper right corner with a margin of 20 to top and right
                imagecopy($image, $logo, imagesx($image) - $logoWidth - 20, 20, 0, 0, $logoWidth, $logoHeight);
            } else {
                // put in the center, vertically a little bit higher than the middle because of the watermark at the bottom
                imagecopy($image, $logo, (imagesx($image) / 2) - ($logoWidth / 2), (imagesy($image) / 2) - ($logoHeight / 2) - 25, 0, 0, $logoWidth, $logoHeight);
            }
        }

        // Read the share images content to a variable
        ob_start();
        imagejpeg($image);
        $shareImageData = ob_get_contents();
        ob_end_clean();

        // Store the new image data to the filesystem, overwrite if file exists
        $adapter = $filesystem->getAdapter();
        // Set Metadata to S3 (doesn't work in unit tests when using memory filesystem)
        if ($adapter instanceof \Gaufrette\Adapter\MetadataSupporter) {
            $adapter->setMetadata($shareImagePath, array('ContentType' => 'image/jpeg', 'ACL' => 'public-read'));
        }
        $adapter->write($shareImagePath, $shareImageData);

        return true;
    }
}
