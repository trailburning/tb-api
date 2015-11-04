<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\DataTransformer\GeometryPointTransformer;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('about', 'textarea')
            ->add('journey', 'entity', [
                'class' => 'AppBundle:Journey',
            ])
            ->add($builder
                ->create('coords')
                ->addModelTransformer(new GeometryPointTransformer()))
            ->add('position')
            ->add('custom', 'burgov_key_value', [
                'value_type' => 'text',
                'use_container_object' => true,
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Event',
            'csrf_protection' => false,
        ]);
    }

    public function getName()
    {
        return '';
    }
}
