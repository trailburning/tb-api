<?php

namespace AppBundle\Tests;

use Liip\FunctionalTestBundle\Test\WebTestCase;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Symfony\Bundle\FrameworkBundle\Client;

abstract class BaseWebTestCase extends WebTestCase
{   
    public function __construct($name = null, array $data = array(), $dataName = '')
    {      
        date_default_timezone_set('UTC');
        parent::__construct($name, $data, $dataName);
    }
    
    protected function loadFixtures(array $classNames, $omName = null, $registryName = 'doctrine', $purgeMode = null)
    {
        return parent::loadFixtures($classNames, $omName, $registryName, ORMPurger::PURGE_MODE_TRUNCATE);
    }
    
    protected function refreshEntity($entity) 
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->refresh($entity);
    }
    
    protected function callProtectedMethod($obj, $methodName, $parameter = array())
    {
        $method = new \ReflectionMethod($obj, $methodName);
        $method->setAccessible(true);
        
        return $method->invokeArgs($obj, $parameter);
    }
    
    protected function debugClient(Client $client) 
    {
        $filepath = $this->getContainer()->get('kernel')->getRootDir() . '/../web/debug.html';
        $content = $client->getResponse()->getContent();
        file_put_contents($filepath, $content);
        die;
    }
    
    protected function assertJsonResponse($client)
    {
        $this->assertEquals('application/json',  $client->getResponse()->headers->get('Content-Type'), 
            'Content-Type Header is "application/json"');  
        $this->assertJson($client->getResponse()->getContent(), 
            'Response is Valid JSON');
    }
    
    protected function getJourney($name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $journeyRepository = $em->getRepository('AppBundle:Journey');
        $journey = $journeyRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$journey) {
            $this->fail(sprintf('Missing journey with name "%s" in test DB', $name));
        }

        return $journey;
    }
    
    protected function getUser($name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository('AppBundle:User');
        $user = $userRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$user) {
            $this->fail(sprintf('Missing user with name "%s" in test DB', $name));
        }

        return $user;
    }
}
