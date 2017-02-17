<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\RaceEvent;
use AppBundle\Entity\RaceEventCompleted;
use Tests\AppBundle\BaseWebTestCase;

class RaceEventCompletedControllerTest extends BaseWebTestCase
{
    public function testPostActionGreen()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\UserData',
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);
        $this->updateSearchIndex();

        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        /** @var RaceEvent $raceEvent */
        $raceEvent = $raceEventRepository->findAll()[0];
        $this->assertNull($raceEvent->getRating());
        $user = $this->getUser('mattallbeury@trailburning.com');
        $token = $this->loginUser($user->getEmail(), 'password', $client);

        $data = [
            'rating' => 5,
            'comment' => 'test comment',
        ];

        $client->request('POST', $this->getRaceEventCompletedUrl($raceEvent->getOid()), $data, [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertJsonResponse($client->getResponse(), 201);
        $this->refreshEntity($raceEvent);
        $this->assertEquals(5.0, $raceEvent->getRating());
    }

    public function testPostActionRaceEventNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
            'AppBundle\DataFixtures\ORM\UserData',
        ]);
        $client = $this->makeClient();
        $user = $this->getUser('mattallbeury@trailburning.com');
        $token = $this->loginUser($user->getEmail(), 'password', $client);
        $data = [];

        $client->request('POST', $this->getRaceEventCompletedUrl('invalidId'), $data, [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testPostActionAccessDenied()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);
        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        /** @var RaceEvent $raceEvent */
        $raceEvent = $raceEventRepository->findAll()[0];
        $data = [];

        $client->request('POST', $this->getRaceEventCompletedUrl($raceEvent->getOid()), $data);
        $this->assertJsonResponse($client->getResponse(), 401);
    }

    public function testPutActionGreen()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\UserData',
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        /** @var RaceEvent $raceEvent */
        $raceEvent = $raceEventRepository->findAll()[0];
        $raceEvent->setRating(1);
        $raceEventRepository->add($raceEvent);
        $raceEventRepository->store();
        $user = $this->getUser('mattallbeury@trailburning.com');
        $token = $this->loginUser($user->getEmail(), 'password', $client);
        $raceEventCompletedRepository = $this->getContainer()->get('app.repository.race_event_completed');
        $raceEventCompleted = new RaceEventCompleted();
        $raceEventCompleted->setRaceEvent($raceEvent);
        $raceEventCompleted->setUser($user);
        $raceEventCompleted->setRating(1);
        $raceEventCompleted->setComment('test comment');
        $raceEventCompletedRepository->add($raceEventCompleted);
        $raceEventCompletedRepository->store();
        $this->updateSearchIndex();
        $data = [
            'rating' => 5,
            'comment' => 'test comment edited',
        ];

        $client->request('PUT', $this->getRaceEventCompletedUrl($raceEvent->getOid()), $data, [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());

        $raceEventCompletedRepository->refresh($raceEventCompleted);
        $this->assertEquals(5, $raceEventCompleted->getRating());
        $this->assertEquals('test comment edited', $raceEventCompleted->getComment());

        $this->refreshEntity($raceEvent);
        $this->assertEquals(5.0, $raceEvent->getRating());
    }

    public function testPutActionAccessDenied()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);
        $client = $this->makeClient();
        $data = [
            'rating' => 5,
            'comment' => 'test comment edited',
        ];

        $client->request('POST', $this->getRaceEventCompletedUrl('someid'), $data);
        $this->assertJsonResponse($client->getResponse(), 401);
    }

    public function testDeleteAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\UserData',
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        /** @var RaceEvent $raceEvent */
        $raceEvent = $raceEventRepository->findAll()[0];
        $raceEvent->setRating(1);
        $raceEventRepository->add($raceEvent);
        $raceEventRepository->store();
        $user = $this->getUser('mattallbeury@trailburning.com');
        $token = $this->loginUser($user->getEmail(), 'password', $client);
        $raceEventCompletedRepository = $this->getContainer()->get('app.repository.race_event_completed');
        $raceEventCompleted = new RaceEventCompleted();
        $raceEventCompleted->setRaceEvent($raceEvent);
        $raceEventCompleted->setUser($user);
        $raceEventCompleted->setRating(1);
        $raceEventCompleted->setComment('test comment');
        $raceEventCompletedRepository->add($raceEventCompleted);
        $raceEventCompletedRepository->store();
        $this->updateSearchIndex();

        $client->request('DELETE', $this->getRaceEventCompletedUrl($raceEvent->getOid()), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $this->refreshEntity($raceEvent);
        $this->assertNull($raceEvent->getRating());
    }

    public function testDeleteActionRaceEventNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
            'AppBundle\DataFixtures\ORM\UserData',
        ]);
        $client = $this->makeClient();
        $user = $this->getUser('mattallbeury@trailburning.com');
        $token = $this->loginUser($user->getEmail(), 'password', $client);

        $client->request('DELETE', $this->getRaceEventCompletedUrl('invalidId'), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testDeleteActionAcceddDenied()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);
        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        /** @var RaceEvent $raceEvent */
        $raceEvent = $raceEventRepository->findAll()[0];
        $data = [];

        $client->request('DELETE', $this->getRaceEventCompletedUrl($raceEvent->getOid()), $data);
        $this->assertJsonResponse($client->getResponse(), 401);
    }

    public function testDeleteActionRaceEventCompletedNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\UserData',
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);

        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        /** @var RaceEvent $raceEvent */
        $raceEvent = $raceEventRepository->findAll()[0];
        $user = $this->getUser('mattallbeury@trailburning.com');
        $token = $this->loginUser($user->getEmail(), 'password', $client);

        $client->request('DELETE', $this->getRaceEventCompletedUrl($raceEvent->getOid()), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    private function getRaceEventCompletedUrl($raceEventId)
    {
        $url = '/v2/user/raceevents/'.$raceEventId.'/completed';

        return $url;
    }
}
