<?php

namespace AppBundle\Services\MetadataReader;

use Symfony\Component\HttpFoundation\File\File;

class JPEGMetadataReader implements MetadataReader
{
    const EXIF_ORIENTATION_TOP_LEFT_SIDE = 1;

    const EXIF_ORIENTATION_TOP_RIGHT_SIDE = 2;

    const EXIF_ORIENTATION_BOTTOM_RIGHT_SIDE = 3;

    const EXIF_ORIENTATION_BOTTOM_LEFT_SIDE = 4;

    const EXIF_ORIENTATION_LEFT_SIDE_TOP = 5;

    const EXIF_ORIENTATION_RIGHT_SIDE_TOP = 6;

    const EXIF_ORIENTATION_RIGHT_SIDE_BOTTOM = 7;

    const EXIF_ORIENTATION_LEFT_SIDE_BOTTOM = 8;

    /**
     * @param File $file
     *
     * @return array
     */
    public function read(File $file)
    {
        $exiftags = exif_read_data($file->getPathname());
        $metadata = [];

        if (isset($exiftags['FileSize'])) {
            $metadata['filesize'] = $exiftags['FileSize'];
        }

        $metadata = array_merge($metadata, $this->getGPSFromExifData($exiftags));
        $metadata = array_merge($metadata, $this->getDimensionsFromExifData($exiftags));

        return $metadata;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function getGPSFromExifData(array $data)
    {
        $metadata = [];
        if (isset($data['GPSLongitude']) && isset($data['GPSLongitudeRef'])
            && isset($data['GPSLatitude']) && isset($data['GPSLatitudeRef'])) {
            $metadata['longitude'] = $this->getGps($data['GPSLongitude'], $data['GPSLongitudeRef']);
            $metadata['latitude'] = $this->getGps($data['GPSLatitude'], $data['GPSLatitudeRef']);
        }

        return $metadata;
    }

    /**
     * @param array $data
     *
     * @return array
     */
    private function getDimensionsFromExifData(array $data)
    {
        if (!isset($data['COMPUTED'])) {
            return[];
        }

        $computed = $data['COMPUTED'];
        if (!isset($computed['Width']) || !isset($computed['Height'])) {
            return[];
        }

        if (isset($data['Orientation'])
            && in_array($data['Orientation'], [
                self::EXIF_ORIENTATION_LEFT_SIDE_TOP,
                self::EXIF_ORIENTATION_RIGHT_SIDE_TOP,
                self::EXIF_ORIENTATION_RIGHT_SIDE_BOTTOM,
                self::EXIF_ORIENTATION_LEFT_SIDE_BOTTOM, ])) {
            $metadata = [
                'height' => $computed['Width'],
                'width' => $computed['Height'],
            ];

            return $metadata;
        }

        $metadata = [
            'height' => $computed['Height'],
            'width' => $computed['Width'],
        ];

        return $metadata;
    }

    /**
     * Helper Function to extract and format GPS coordinates from EXIF data.
     *
     * @param $exifCoord
     * @param $hemi
     *
     * @return int
     */
    protected function getGps($exifCoord, $hemi)
    {
        $degrees = 0;
        if (count($exifCoord) > 0) {
            $degrees = $this->gps2Num($exifCoord[0]);
        }

        $minutes = 0;
        if (count($exifCoord) > 1) {
            $minutes = $this->gps2Num($exifCoord[1]);
        }

        $seconds = 0;
        if (count($exifCoord) > 2) {
            $seconds = $this->gps2Num($exifCoord[2]);
        }

        $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

        return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
    }

    /**
     * Helper function to extract and format GPS coordinates from EXIF data.
     *
     * @param $coordPart
     *
     * @return float|int
     */
    protected function gps2Num($coordPart)
    {
        $parts = explode('/', $coordPart);
        if (count($parts) <= 0) {
            return 0;
        }

        if (count($parts) == 1) {
            return $parts[0];
        }

        return floatval($parts[0]) / floatval($parts[1]);
    }
}
