<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\DataTransformer\AssetCategoryTransformer;
use AppBundle\Repository\AssetCategoryRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

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
            ->add('about', TextareaType::class)
            ->add('event', EntityType::class, [
                'class' => 'AppBundle:Event',
            ])
            ->add('category', EntityType::class, [
                'class' => 'AppBundle:AssetCategory',
            ])
            ->add('position')
            ->add('credit')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Asset',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
