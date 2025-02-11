<?php

namespace AppBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseRegistrationFormType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\DataTransformer\GeometryPointTransformer;
use AppBundle\Entity\User;

class RegistrationFormType extends BaseRegistrationFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        // add your custom fields not defined in FOSUserBundle
        $builder->add('firstName');
        $builder->add('lastName');
        $builder->add($builder->create('coords')->addModelTransformer(new GeometryPointTransformer()));
        $builder->add('location');
        $builder->add('about', 'textarea');
        $builder->add('gender', 'choice', [
            'choices' => [
                User::GENDER_NONE => 'I\'d rather not say',
                User::GENDER_MALE => 'Male',
                User::GENDER_FEMALE => 'Female',
            ],
        ]);
        $builder->add('social_media');
        $builder->add('race_event_type');
        $builder->add('race_distance_max');
        $builder->add('race_distance_min');
        
        // remove username, we user email adress as username instead
        $builder->remove('username');
        // $builder->remove('plainPassword');
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('csrf_protection', false);
    }
}
