<?php

namespace AppBundle\Form\Type;

use FOS\UserBundle\Form\Type\ProfileFormType as BaseProfileFormType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormBuilderInterface;
use AppBundle\Form\DataTransformer\GeometryPointTransformer;
use AppBundle\Entity\User;

class ProfileFormType extends BaseProfileFormType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildUserForm($builder, $options);

        // add your custom fields not defined in FOSUserBundle
        $builder->add('firstName');
        $builder->add('lastName');
        $builder->add($builder->create('coords')->addModelTransformer(new GeometryPointTransformer()));
        $builder->add('location');
        $builder->add('about', TextareaType::class);
        $builder->add('gender', ChoiceType::class, [
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
        
        $builder->remove('username');
        $builder->remove('email');
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

