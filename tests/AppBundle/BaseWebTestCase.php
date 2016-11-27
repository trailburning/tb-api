<?php

namespace tests\AppBundle;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Liip\FunctionalTestBundle\Test\WebTestCase;
use Symfony\Bundle\FrameworkBundle\Client;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\HttpFoundation\Response;
use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use AppBundle\Services\MapboxAPI;

abstract class BaseWebTestCase extends WebTestCase
{
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        date_default_timezone_set('UTC');
        parent::__construct($name, $data, $dataName);
    }

    protected function makeClient($authentication = false, array $params = array())
    {
        $client = parent::makeClient($authentication, $params);
        $client->getContainer()->set('media_filesystem', $client->getContainer()->get('debug_filesystem'));

        return $client;
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
        $filepath = $this->getContainer()->get('kernel')->getRootDir().'/../web/debug.html';
        $content = $client->getResponse()->getContent();
        file_put_contents($filepath, $content);
        die;
    }

    protected function assertJsonResponse($response, $statusCode)
    {
        $this->assertEquals($statusCode, $response->getStatusCode(), $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), $response->headers);
        $this->assertJson($response->getContent(), $response->getContent());
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

    protected function loginUser($username, $password, $client)
    {
        $data = [
            'username' => $username,
            'password' => $password,
        ];

        $client->request('POST', '/v2/user/login', $data);
        if ($client->getResponse()->getStatusCode() !== 200) {
            throw new \Exception('Invalid login credentials');
        }
        $responseContent = $client->getResponse()->getContent();
        $responseObj = json_decode($responseContent);
        $token = $responseObj->token;

        return $token;
    }

    protected function getUser($name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository('AppBundle:User');
        $user = $userRepository->findOneBy([
            'username' => $name,
        ]);

        if (!$user) {
            $this->fail(sprintf('Missing user with name "%s" in test DB', $name));
        }

        return $user;
    }

    protected function getEvent($name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $eventRepository = $em->getRepository('AppBundle:Event');
        $event = $eventRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$event) {
            $this->fail(sprintf('Missing event with name "%s" in test DB', $name));
        }

        return $event;
    }

    protected function getAsset($name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $assetRepository = $em->getRepository('AppBundle:Asset');
        $asset = $assetRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$asset) {
            $this->fail(sprintf('Missing asset with name "%s" in test DB', $name));
        }

        return $asset;
    }

    protected function getRaceEvent($name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $raceEventRepository = $em->getRepository('AppBundle:RaceEvent');
        $raceEvent = $raceEventRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$raceEvent) {
            $this->fail(sprintf('Missing race event with name "%s" in test DB', $name));
        }

        return $raceEvent;
    }

    protected function getAssetCategory($name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $assetCategoryRepository = $em->getRepository('AppBundle:AssetCategory');
        $assetCategory = $assetCategoryRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$assetCategory) {
            $this->fail(sprintf('Missing asset category with name "%s" in test DB', $name));
        }

        return $assetCategory;
    }

    protected function updateSearchIndex()
    {
        $kernel = $this->getContainer()->get('kernel');
        $application = new Application($kernel);
        $application->setAutoExit(false);

        $input = new ArrayInput(array(
           'command' => 'app:search:index',
           'type' => 'race_event',
           '--env' => 'test',
        ));

        $output = new BufferedOutput();
        $application->run($input, $output);

        $content = $output->fetch();

        return new Response($content);
    }

    protected function getMapboxAPIMock()
    {
        $mock = new MockHandler([
            new GuzzleResponse(200, [], file_get_contents(__DIR__.'/../DataFixtures/Mapbox/reverse_geocode.json')),
        ]);

        $handler = HandlerStack::create($mock);
        $client = new GuzzleClient(['handler' => $handler]);
        $mapbox = new MapboxAPI($client, 'token');

        return $mapbox;
    }
}
