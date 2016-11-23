<?php

namespace AppBundle\Tests\Controller;

use Tests\AppBundle\BaseWebTestCase;

class ProfileControllerTest extends BaseWebTestCase
{
    public function testPutAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\UserData',
        ]);

        $client = $this->makeClient();
        $data = [
            'firstName' => 'first',
            'lastName' => 'last',
            'gender' => 1,
            'location' => '(13.221316, 52.489695)',
            'social_media' => 'http://faceboom.com',
            'race_event_type' => 'trail_run',
            'race_distance_max' => 30000,
            'race_distance_min' => 10000,
        ];
        $user = $this->getUser('mattallbeury@trailburning.com');
        $token = $this->loginUser($user->getEmail(), 'password', $client);

        $client->request('PUT', '/v2/user', $data, [], [
            'HTTP_AUTHORIZATION' => 'Bearer '. $token,
        ]);
        // var_export($client->getResponse());
        // exit;
        $this->assertJsonResponse($client->getResponse(), 204);
    }

    public function testPostActionAccessDenied()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\UserData',
        ]);

        $client = $this->makeClient();
        $data = [];

        $user = $this->getUser('mattallbeury@trailburning.com');
        $client->request('PUT', '/v2/user', $data);
        $this->assertJsonResponse($client->getResponse(), 401);
    }
}
