<?php

namespace AppBundle\Form\Type;

use AppBundle\Form\DataTransformer\GeometryPointTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class EventType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('about', TextareaType::class)
            ->add('journey', EntityType::class, [
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
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Event',
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
