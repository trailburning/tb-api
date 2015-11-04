<?php

namespace AppBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use AppBundle\Repository\AssetCategoryRepository;

class AssetCategoryTransformer implements DataTransformerInterface
{
    /**
     * @var AssetCategoryRepository
     */
    protected $assetCategoryRepository;

    /**
     * @param AssetCategoryRepository $assetCategoryRepository
     */
    public function __construct(AssetCategoryRepository $assetCategoryRepository)
    {
        $this->assetCategoryRepository = $assetCategoryRepository;
    }

    /**
     * Transforms a string to a Point object.
     *
     * @param string $name
     *
     * @return int|null
     *
     * @throws TransformationFailedException
     */
    public function transform($id)
    {   
        if (null === $id) {
            return;
        }
        
        $assetCategory = $this->assetCategoryRepository->findOneBy([
            'id' => $id,
        ]);
        if ($assetCategory === null) {
            throw new TransformationFailedException('Asset category not found');
        }

        return $assetCategory;
    }
    
    /**
     * @param int $id
     *
     * @return string
     */
    public function reverseTransform($name)
    {
        if (null === $name) {
            return;
        }
        
        $assetCategory = $this->assetCategoryRepository->findOneBy([
            'name' => $name,
        ]);
        if ($assetCategory === null) {
            throw new TransformationFailedException('Asset category not found');
        }
        
        return $assetCategory->getId();
    }
}
