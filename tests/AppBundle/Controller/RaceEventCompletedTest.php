<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\RaceEvent;
use AppBundle\Entity\RaceEventCompleted;
use Tests\AppBundle\BaseWebTestCase;

class RaceEventCompletedTest extends BaseWebTestCase
{
    public function testPostActionGreen()
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
        $data = [
            'rating' => 5,
            'comment' => 'test comment',
        ];

        $client->request('POST', $this->getRaceEventCompletedUrl($raceEvent->getOid(), $user->getId()), $data);
        $this->assertJsonResponse($client->getResponse(), 201);
    }

    public function testPostActionRaceEventNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
            'AppBundle\DataFixtures\ORM\UserData',
        ]);
        $client = $this->makeClient();
        $user = $this->getUser('mattallbeury@trailburning.com');
        $data = [];

        $client->request('POST', $this->getRaceEventCompletedUrl('invalidId', $user->getId()), $data);
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testPostActionUserNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);
        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        /** @var RaceEvent $raceEvent */
        $raceEvent = $raceEventRepository->findAll()[0];
        $data = [];

        $client->request('POST', $this->getRaceEventCompletedUrl($raceEvent->getOid(), 0), $data);
        $this->assertJsonResponse($client->getResponse(), 404);
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
        $user = $this->getUser('mattallbeury@trailburning.com');
        $raceEventCompletedRepository = $this->getContainer()->get('app.repository.race_event_completed');
        $raceEventCompleted = new RaceEventCompleted();
        $raceEventCompleted->setRaceEvent($raceEvent);
        $raceEventCompleted->setUser($user);
        $raceEventCompleted->setRating(1);
        $raceEventCompleted->setComment('test comment');
        $raceEventCompletedRepository->add($raceEventCompleted);
        $raceEventCompletedRepository->store();
        $data = [
            'rating' => 5,
            'comment' => 'test comment edited',
        ];

        $client->request('PUT', $this->getRaceEventCompletedUrl($raceEvent->getOid(), $user->getId()), $data);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());

        $raceEventCompletedRepository->refresh($raceEventCompleted);
        $this->assertEquals(5, $raceEventCompleted->getRating());
        $this->assertEquals('test comment edited', $raceEventCompleted->getComment());

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
        $user = $this->getUser('mattallbeury@trailburning.com');
        $raceEventCompletedRepository = $this->getContainer()->get('app.repository.race_event_completed');
        $raceEventCompleted = new RaceEventCompleted();
        $raceEventCompleted->setRaceEvent($raceEvent);
        $raceEventCompleted->setUser($user);
        $raceEventCompleted->setRating(1);
        $raceEventCompleted->setComment('test comment');
        $raceEventCompletedRepository->add($raceEventCompleted);
        $raceEventCompletedRepository->store();

        $client->request('DELETE', $this->getRaceEventCompletedUrl($raceEvent->getOid(), $user->getId()));
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testDeleteActionRaceEventNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
            'AppBundle\DataFixtures\ORM\UserData',
        ]);
        $client = $this->makeClient();
        $user = $this->getUser('mattallbeury@trailburning.com');

        $client->request('DELETE', $this->getRaceEventCompletedUrl('invalidId', $user->getId()));
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testDeleteActionUserNotFound()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);
        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        /** @var RaceEvent $raceEvent */
        $raceEvent = $raceEventRepository->findAll()[0];
        $data = [];

        $client->request('DELETE', $this->getRaceEventCompletedUrl($raceEvent->getOid(), 0), $data);
        $this->assertJsonResponse($client->getResponse(), 404);
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

        $client->request('DELETE', $this->getRaceEventCompletedUrl($raceEvent->getOid(), $user->getId()));
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    private function getRaceEventCompletedUrl($raceEventId, $userId)
    {
        $url = '/v2/raceevents/'.$raceEventId.'/user/'.$userId.'/completed';

        return $url;
    }
}
