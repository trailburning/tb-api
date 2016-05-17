<?php

namespace AppBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class AWSDebugCommand extends ContainerAwareCommand
{
    protected function configure()
    {
        $this
            ->setName('mlm:aws:debug');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $mediaService = $this->getContainer()->get('app.media');
        $filesystem = $this->getContainer()->get('media_filesystem');
        $mediaService->setFilesystem($filesystem);

        $file = new UploadedFile(
            realpath(__DIR__.'/../DataFixtures/Media/test.jpg'),
            'test.jpg'
        );

        $filepath = $mediaService->uploadFile($file);
        echo $filepath;
    }
}
