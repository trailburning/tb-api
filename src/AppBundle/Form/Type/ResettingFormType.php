<?php

namespace AppBundle\Form\Type;

use FOS\UserBundle\Form\Type\ResettingFormType as BaseResettingFormType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ResettingFormType extends BaseResettingFormType
{
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefault('csrf_protection', false);
    }
}
