<?php

namespace AppBundle\Tests\Controller;

use AppBundle\Entity\User;
use Symfony\Bundle\SwiftmailerBundle\DataCollector\MessageDataCollector;
use Tests\AppBundle\BaseWebTestCase;

class RegistrationControllerTest extends BaseWebTestCase
{

    public function testRegisterAction()
    {
        $this->loadFixtures([]);

        $client = $this->makeClient();
        $client->enableProfiler();
        $data = [
            'email' => 'name@mail.com',
            'plainPassword' => [
                'first' => 'test',
                'second' => 'test',
            ],
            'firstName' => 'first',
            'lastName' => 'last',
            'gender' => 1,
            'coords' => '(13.221316, 52.489695)',
            'social_media' => 'http://facebook.com',
            'race_event_type' => 'trail_run',
            'race_distance_max' => 30000,
            'race_distance_min' => 10000,
        ];
        
        $client->request('POST', '/v2/user/register', $data);
        $this->assertJsonResponse($client->getResponse(), 201);

        /** @var MessageDataCollector$mailCollector */
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        /** @var \Swift_Message $message */
        $message = $collectedMessages[0];

        // Asserting email data
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals('Welcome to RaceBase World!', $message->getSubject());
        $this->assertEquals('hello@racebase.world', key($message->getFrom()));
        $this->assertEquals('name@mail.com', key($message->getTo()));
        
        $user = $this->getUser('name@mail.com');
        $this->assertFalse($user->isEnabled());
        $this->assertEquals('race_base', $user->getClient());
    }

    public function testPostActionBadRequest()
    {
        $client = $this->makeClient();
        $data = [];

        $client->request('POST', '/v2/user/register', $data);
        $this->assertJsonResponse($client->getResponse(), 400);
    }
    
    public function testConfirmInvalidTokenAction()
    {
        $this->loadFixtures([]);

        $client = $this->makeClient();

        $client->request('GET', '/v2/user/confirm/invalidtoken');
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testConfirmTokenAction()
    {
        $this->loadFixtures([]);

        $client = $this->makeClient();
        $userManager = $client->getContainer()->get('fos_user.user_manager');
        $userRepository = $client->getContainer()->get('app.user.repository');
        /** @var User $user */
        $user = $userManager->createUser();
        $user->setEnabled(false);
        $user->setEmail('test@test');
        $user->setPassword('test');
        $user->setFirstName('first');
        $user->setLastName('last');
        $user->setGender(1);
        $user->setConfirmationToken('testtoken');
        $userManager->updateUser($user);

        $client->request('GET', '/v2/user/confirm/testtoken');
        $this->assertJsonResponse($client->getResponse(), 200);

        $userRepository->refresh($user);
        $this->assertTrue($user->isEnabled());

        $response = $client->getResponse()->getContent();
        $responseObj = json_decode($response);
        $this->assertObjectHasAttribute('token', $responseObj);

    }

}
