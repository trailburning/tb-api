<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\DataTransformer\GeometryPointTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class RaceEventType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('about', TextareaType::class)
            ->add('website')
            ->add($builder
                ->create('coords')
                ->addModelTransformer(new GeometryPointTransformer()))
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\RaceEvent',
            'csrf_protection' => false,
        ));
    }
}
