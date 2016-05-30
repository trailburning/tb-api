<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use AppBundle\Form\DataTransformer\GeometryPointTransformer;

class SearchType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array                $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('q')
            ->add('dateFrom', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add('dateTo', DateType::class, [
                'widget' => 'single_text',
            ])
            ->add($builder
                ->create('coords')
                ->addModelTransformer(new GeometryPointTransformer()))
            ->add('distance', IntegerType::class)
            ->add('type')
            ->add('category')
            ->add('order')
            ->add('sort')
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Model\Search',
            'csrf_protection' => false,
        ));
    }
}
