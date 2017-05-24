<?php

namespace tests\AppBundle;

use AppBundle\Entity\Asset;
use AppBundle\Entity\AssetCategory;
use AppBundle\Entity\Event;
use AppBundle\Entity\Journey;
use AppBundle\Entity\RaceEvent;
use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\Executor\AbstractExecutor;
use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Exception;
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
    /**
     * BaseWebTestCase constructor.
     *
     * @param null   $name
     * @param array  $data
     * @param string $dataName
     */
    public function __construct($name = null, array $data = array(), $dataName = '')
    {
        date_default_timezone_set('UTC');
        parent::__construct($name, $data, $dataName);
    }

    /**
     * @param bool  $authentication
     * @param array $params
     *
     * @return Client
     */
    protected function makeClient($authentication = false, array $params = array())
    {
        $client = parent::makeClient($authentication, $params);
        $client->getContainer()->set('media_filesystem', $client->getContainer()->get('debug_filesystem'));

        return $client;
    }

    /**
     * @param array  $classNames
     * @param null   $omName
     * @param string $registryName
     * @param null   $purgeMode
     *
     * @return AbstractExecutor|null
     */
    protected function loadFixtures(array $classNames, $omName = null, $registryName = 'doctrine', $purgeMode = null)
    {
        return parent::loadFixtures($classNames, $omName, $registryName, ORMPurger::PURGE_MODE_TRUNCATE);
    }

    /**
     * @param object $entity
     */
    protected function refreshEntity($entity)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $em->refresh($entity);
    }

    /**
     * @param $obj
     * @param $methodName
     * @param array $parameter
     *
     * @return mixed
     */
    protected function callProtectedMethod($obj, $methodName, $parameter = array())
    {
        $method = new \ReflectionMethod($obj, $methodName);
        $method->setAccessible(true);

        return $method->invokeArgs($obj, $parameter);
    }

    /**
     * @param Client $client
     */
    protected function debugClient(Client $client)
    {
        $filepath = $this->getContainer()->get('kernel')->getRootDir().'/../web/debug.html';
        $content = $client->getResponse()->getContent();
        file_put_contents($filepath, $content);
        die;
    }

    /**
     * @param Response $response
     * @param int      $statusCode
     */
    protected function assertJsonResponse(Response $response, int $statusCode)
    {
        $this->assertEquals($statusCode, $response->getStatusCode(), $response->getStatusCode());
        $this->assertTrue($response->headers->contains('Content-Type', 'application/json'), $response->headers);
        $this->assertJson($response->getContent(), $response->getContent());
    }

    /**
     * @param $name
     *
     * @return Journey
     *
     * @throws Exception
     */
    protected function getJourney($name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $journeyRepository = $em->getRepository('AppBundle:Journey');
        $journey = $journeyRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$journey) {
            throw new Exception(sprintf('Missing journey with name "%s" in test DB', $name));
        }

        return $journey;
    }

    /**
     * @param string $username
     * @param string $password
     * @param Client $client
     *
     * @return string
     *
     * @throws Exception
     */
    protected function loginUser(string $username, string $password, Client $client): string
    {
        $data = [
            'username' => $username,
            'password' => $password,
        ];

        $client->request('POST', '/v2/user/login', $data);
        if ($client->getResponse()->getStatusCode() !== 200) {
            throw new Exception('Invalid login credentials');
        }
        $responseContent = $client->getResponse()->getContent();
        $responseObj = json_decode($responseContent);
        $token = $responseObj->token;

        return $token;
    }

    /**
     * @param string $name
     *
     * @return User
     *
     * @throws Exception
     */
    protected function getUser(string $name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $userRepository = $em->getRepository('AppBundle:User');
        $user = $userRepository->findOneBy([
            'username' => $name,
        ]);

        if (!$user) {
            throw new Exception(sprintf('Missing user with name "%s" in test DB', $name));
        }

        return $user;
    }

    /**
     * @param string $name
     *
     * @return Event
     *
     * @throws Exception
     */
    protected function getEvent(string $name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $eventRepository = $em->getRepository('AppBundle:Event');
        $event = $eventRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$event) {
            throw new Exception(sprintf('Missing event with name "%s" in test DB', $name));
        }

        return $event;
    }

    /**
     * @param string $name
     *
     * @return Asset
     *
     * @throws Exception
     */
    protected function getAsset(string $name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $assetRepository = $em->getRepository('AppBundle:Asset');
        $asset = $assetRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$asset) {
            throw new Exception(sprintf('Missing asset with name "%s" in test DB', $name));
        }

        return $asset;
    }

    /**
     * @param string $name
     *
     * @return RaceEvent
     *
     * @throws Exception
     */
    protected function getRaceEvent(string $name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $raceEventRepository = $em->getRepository('AppBundle:RaceEvent');
        $raceEvent = $raceEventRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$raceEvent) {
            throw new Exception(sprintf('Missing race event with name "%s" in test DB', $name));
        }

        return $raceEvent;
    }

    /**
     * @param string $name
     *
     * @return AssetCategory
     *
     * @throws Exception
     */
    protected function getAssetCategory(string $name)
    {
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');
        $assetCategoryRepository = $em->getRepository('AppBundle:AssetCategory');
        $assetCategory = $assetCategoryRepository->findOneBy([
            'name' => $name,
        ]);

        if (!$assetCategory) {
            throw new Exception(sprintf('Missing asset categorywith name "%s" in test DB', $name));
        }

        return $assetCategory;
    }

    /**
     * @return Response
     */
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

    /**
     * @return MapboxAPI
     */
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

    /**
     * @param RaceEvent $raceEvent
     */
    protected function createRaceEventInSearch(RaceEvent $raceEvent)
    {
        $searchIndexService = $this->getContainer()->get('app.services.searchindex');
        $searchIndexService->createRaceEvent($raceEvent);
    }
}
