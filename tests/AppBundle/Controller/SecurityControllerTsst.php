<?php

namespace AppBundle\Tests\Controller;

use Tests\AppBundle\BaseWebTestCase;

class SecurityControllerTsst extends BaseWebTestCase
{

    public function testLoginAction()
    {
        $this->loadFixtures([
            'AppBundle\DataFixtures\ORM\UserData',
        ]);

        $client = $this->makeClient();
        $data = [
            'username' => 'mattallbeury@trailburning.com',
            'password' => 'password',
        ];
        
        $client->request('POST', '/v2/user/login', $data);
        $this->assertJsonResponse($client->getResponse(), 200);
    }
    
    public function testLoginWrongCredentialsAction()
    {
        $this->loadFixtures([]);

        $client = $this->makeClient();
        $data = [
            'username' => 'mattallbeury@trailburning.com',
            'password' => 'password',
        ];
        
        $client->request('POST', '/v2/user/login', $data);
        $this->assertJsonResponse($client->getResponse(), 401);
    }
}
