<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\RaceEvent;
use AppBundle\Entity\RaceEventWishlist;
use Tests\AppBundle\BaseWebTestCase;

class RaceEventWishlistControllerTest extends BaseWebTestCase
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
        $token = $this->loginUser($user->getEmail(), 'password', $client);

        $client->request('POST', $this->getRaceEventWishlistUrl($raceEvent->getOid()), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

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
        $token = $this->loginUser($user->getEmail(), 'password', $client);

        $client->request('POST', $this->getRaceEventWishlistUrl('invalidId'), [], [], [
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

        $client->request('POST', $this->getRaceEventWishlistUrl($raceEvent->getOid()));
        $this->assertJsonResponse($client->getResponse(), 401);
    }

    public function testPostActionAlreadyExist()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
            'AppBundle\DataFixtures\ORM\UserData',
        ]);
        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        /** @var RaceEvent $raceEvent */
        $raceEvent = $raceEventRepository->findAll()[0];
        $user = $this->getUser('mattallbeury@trailburning.com');
        $raceEventWishlistRepository = $this->getContainer()->get('app.repository.race_event_wishlist');
        $raceEventWishlist = new RaceEventWishlist();
        $raceEventWishlist->setRaceEvent($raceEvent);
        $raceEventWishlist->setUser($user);
        $raceEventWishlistRepository->add($raceEventWishlist);
        $raceEventWishlistRepository->store();

        $token = $this->loginUser($user->getEmail(), 'password', $client);

        $client->request('POST', $this->getRaceEventWishlistUrl($raceEvent->getOid()), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertJsonResponse($client->getResponse(), 400);
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
        $raceEventWishlistRepository = $this->getContainer()->get('app.repository.race_event_wishlist');
        $raceEventWishlist = new RaceEventWishlist();
        $raceEventWishlist->setRaceEvent($raceEvent);
        $raceEventWishlist->setUser($user);
        $raceEventWishlistRepository->add($raceEventWishlist);
        $raceEventWishlistRepository->store();

        $token = $this->loginUser($user->getEmail(), 'password', $client);

        $client->request('DELETE', $this->getRaceEventWishlistUrl($raceEvent->getOid()), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

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
        $token = $this->loginUser($user->getEmail(), 'password', $client);

        $client->request('DELETE', $this->getRaceEventWishlistUrl('invalidId'), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testDeleteActionAccessDenied()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\RaceEventData',
        ]);
        $client = $this->makeClient();
        $raceEventRepository = $this->getContainer()->get('app.repository.race_event');
        /** @var RaceEvent $raceEvent */
        $raceEvent = $raceEventRepository->findAll()[0];

        $client->request('DELETE', $this->getRaceEventWishlistUrl($raceEvent->getOid()));
        $this->assertJsonResponse($client->getResponse(), 401);
    }

    public function testDeleteActionRaceEventWishlistNotFound()
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

        $client->request('DELETE', $this->getRaceEventWishlistUrl($raceEvent->getOid()), [], [], [
            'HTTP_AUTHORIZATION' => 'Bearer '.$token,
        ]);

        $this->assertJsonResponse($client->getResponse(), 404);
    }

    private function getRaceEventWishlistUrl($raceEventId)
    {
        $url = '/v2/user/raceevents/'.$raceEventId.'/wishlist';

        return $url;
    }
}
