<?php


namespace AppBundle\Form\DataTransformer;

use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class GeometryPointTransformer implements DataTransformerInterface
{
    public function __construct()
    {
    }

    /**
     * Transforms a Point object to a string.
     *
     * @param Point|null $point
     *
     * @return string
     */
    public function transform($point)
    {
        if (null === $point) {
            return;
        }

        return sprintf('(%s, %s)', $point->getLongitude(), $point->getLatitude());
    }

    /**
     * Transforms a string to a Point object.
     *
     * @param string $point
     *
     * @return Point|null
     *
     * @throws TransformationFailedException if string cannot be converted to a Point
     */
    public function reverseTransform($point)
    {
        if (null === $point) {
            return;
        }

        // check the location Sting format
        if (!preg_match('/^\(([-\d]+\.[-\d]+),\s?([-\d]+\.[-\d]+)\)$/', $point, $match)) {
            throw new TransformationFailedException(sprintf('Invalid point string format: %s', $point));
        }
        $point = new Point($match[1], $match[2], 4326);

        return $point;
    }
}
