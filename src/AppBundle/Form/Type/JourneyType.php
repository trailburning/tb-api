<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class JourneyType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')
            ->add('about', 'textarea')
            ->add('user', 'entity', [
                'class' => 'AppBundle:User',
            ])
            ->add('position')
            ->add('publish', 'checkbox');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Journey',
            'csrf_protection' => false,
        ]);
    }

    public function getName()
    {
        return '';
    }
}
