<?php

namespace AppBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Form\DataTransformer\BooleanTransformer;

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
            ->add('publish', 'choice', array(
                'choices' => array('true' => true, 'false' => false),
            ));
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
