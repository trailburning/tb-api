<?php 

namespace AppBundle\DemoBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OverrideServiceCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $definition = $container->getDefinition('fos_user.registration.form.type');
        $definition->setClass('AppBundle\Form\Type\RegistrationFormType');
    }
}