<?php

namespace AppBundle\Tests\Controller;

use Tests\AppBundle\BaseWebTestCase;

class ResettingControllerTest extends BaseWebTestCase
{
    public function testRequestAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\UserData',
        ]);

        $client = $this->makeClient();
        $client->enableProfiler();
        $data = [
            'email' => 'mattallbeury@trailburning.com',
        ];
        $client->request('POST', '/v2/user/password/request', $data);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
        $mailCollector = $client->getProfile()->getCollector('swiftmailer');
        $this->assertEquals(1, $mailCollector->getMessageCount());
        $collectedMessages = $mailCollector->getMessages();
        $message = $collectedMessages[0];

        // Asserting email data
        $this->assertInstanceOf('Swift_Message', $message);
        $this->assertEquals('Reset Password', $message->getSubject());
        $this->assertEquals('hello@racebase.world', key($message->getFrom()));
        $this->assertEquals('mattallbeury@trailburning.com', key($message->getTo()));

        $user = $this->getUser('mattallbeury@trailburning.com');
    }

    public function testRequestActionInvalidEmail()
    {
        $client = $this->makeClient();
        $data = [
            'email' => 'unknown',
        ];

        $client->request('POST', '/v2/user/password/request', $data);
        $this->assertJsonResponse($client->getResponse(), 400);
    }

    public function testResetAction()
    {
        $this->loadFixtures([]);

        $client = $this->makeClient();
        $userManager = $client->getContainer()->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setEmail('test@test');
        $user->setPassword('test');
        $user->setFirstName('first');
        $user->setLastName('last');
        $user->setGender(1);
        $user->setConfirmationToken('testtoken');
        $userManager->updateUser($user);

        $data = [
            'plainPassword' => [
                'first' => 'new',
                'second' => 'new',
            ],
        ];

        $client->request('POST', '/v2/user/password/reset/testtoken', $data);
        $this->assertEquals(204, $client->getResponse()->getStatusCode());
    }

    public function testResetActionInvalidToken()
    {
        $client = $this->makeClient();
        $data = [
            'email' => 'unknown',
        ];

        $client->request('POST', '/v2/user/password/reset/testtoken', $data);
        $this->assertJsonResponse($client->getResponse(), 404);
    }

    public function testResetActionWrongData()
    {
        $this->loadFixtures([]);

        $client = $this->makeClient();
        $userManager = $client->getContainer()->get('fos_user.user_manager');
        $user = $userManager->createUser();
        $user->setEnabled(true);
        $user->setEmail('test@test');
        $user->setPassword('test');
        $user->setFirstName('first');
        $user->setLastName('last');
        $user->setGender(1);
        $user->setConfirmationToken('testtoken');
        $userManager->updateUser($user);

        $data = [
            'plainPassword' => [
                'first' => 'new',
                'second' => 'other',
            ],
        ];

        $client->request('POST', '/v2/user/password/reset/testtoken', $data);
        $this->assertJsonResponse($client->getResponse(), 400);
    }
}
