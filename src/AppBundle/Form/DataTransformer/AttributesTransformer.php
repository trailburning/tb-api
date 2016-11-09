<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class AttributesTransformer implements DataTransformerInterface
{
    public function __construct()
    {
    }

    /**
     * Transforms a string with comma separated attributes to an array.
     *
     * @param string $attributes
     *
     * @return array
     */
    public function transform($attributes)
    {
        if (null === $attributes) {
            return;
        }

        $attributeArray = explode('.', $attributes);

        return $attributeArray;
    }

    /**
     * Transforms aan attributes array to a string with comma separated attributes.
     *
     * @param array $attributes
     *
     * @return string
     */
    public function reverseTransform($attributes)
    {
        if (count($attributes) === 0) {
            return '';
        }
        
        $attributes = implode(",", $attributes);

        return $attributes;
    }
}
