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
     * @return array
     */
    public function read(File $file) 
    {
        $exiftags = exif_read_data($file->getPathname());
        $metadata = [];

        if (isset($exiftags['FileSize'])) { 
            $metadata['filesize'] = $exiftags['FileSize']; 
        }
        
        if (isset($exif['GPSLongitude']) && isset($exif['GPSLongitudeRef']) && isset($exif['GPSLatitude']) && isset($exif['GPSLatitudeRef'])) {
            $metadata['longitude'] = $this->getGps($exif['GPSLongitude'], $exif['GPSLongitudeRef']);
            $metadata['latitude'] = $this->getGps($exif['GPSLatitude'], $exif['GPSLatitudeRef']);
        }
       
        if (isset($exiftags['Orientation']) 
            && in_array($exiftags['Orientation'], [self::EXIF_ORIENTATION_LEFT_SIDE_TOP, self::EXIF_ORIENTATION_RIGHT_SIDE_TOP, self::EXIF_ORIENTATION_RIGHT_SIDE_BOTTOM, self::EXIF_ORIENTATION_LEFT_SIDE_BOTTOM])) {
            if (isset($exiftags['COMPUTED']) && isset($exiftags['COMPUTED']['Width'])) {
                $metadata['height'] = $exiftags['COMPUTED']['Width']; 
            }
            if (isset($exiftags['COMPUTED']) && isset($exiftags['COMPUTED']['Height'])) {
                $metadata['width'] = $exiftags['COMPUTED']['Height']; 
            }
        } else {
            if (isset($exiftags['COMPUTED']) && isset($exiftags['COMPUTED']['Width'])) {
                $metadata['width'] = $exiftags['COMPUTED']['Width']; 
            }
            if (isset($exiftags['COMPUTED']) && isset($exiftags['COMPUTED']['Height'])) {
                $metadata['height'] = $exiftags['COMPUTED']['Height']; 
            }
        }
        
        return $metadata;
    }   
    
    /**
     * Helper function to extract and format GPS coordinates from EXIF data
     */
    protected function getGps($exifCoord, $hemi) 
    {
        $degrees = count($exifCoord) > 0 ? $this->gps2Num($exifCoord[0]) : 0;
        $minutes = count($exifCoord) > 1 ? $this->gps2Num($exifCoord[1]) : 0;
        $seconds = count($exifCoord) > 2 ? $this->gps2Num($exifCoord[2]) : 0;
        $flip = ($hemi == 'W' or $hemi == 'S') ? -1 : 1;

        return $flip * ($degrees + $minutes / 60 + $seconds / 3600);
    }

    /**
     * Helper function to extract and format GPS coordinates from EXIF data
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