<?php

namespace AppBundle\Form\Type;

use AppBundle\DBAL\Types\MIMEType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;

class MediaUploadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('media', FileType::class, [
                'required' => true,
                'constraints' => [
                    new NotBlank(),
                    new File([
                        'mimeTypes' => MIMEType::getChoices(),
                        'maxSize' => '6M',
                    ]),
                ],
            ])
            ->add('credit')
            ->add('creditUrl')
            ->add('publish')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'csrf_protection' => false,
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
