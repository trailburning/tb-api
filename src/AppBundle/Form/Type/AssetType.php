<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\DataTransformer\AssetCategoryTransformer;
use AppBundle\Repository\AssetCategoryRepository;

class AssetType extends AbstractType
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('about', 'textarea')
            ->add('event', 'entity', [
                'class' => 'AppBundle:Event',
            ])
            ->add('category', 'entity', [
                'class' => 'AppBundle:AssetCategory',
            ])
            ->add('position')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Asset',
            'csrf_protection' => false,
        ]);
    }

    public function getName()
    {
        return '';
    }
}
