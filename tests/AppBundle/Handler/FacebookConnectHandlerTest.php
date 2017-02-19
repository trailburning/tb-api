<?php

namespace Tests\AppBundle\Services;

use AppBundle\Entity\User;
use AppBundle\Handler\FacebookConnectHandler;
use AppBundle\Security\UserManager;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Services\FileUploader;
use Exception;
use Facebook\Facebook;
use Facebook\Http\GraphRawResponse;
use Facebook\HttpClients\FacebookHttpClientInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use PHPUnit_Framework_MockObject_MockObject;
use Symfony\Component\HttpFoundation\JsonResponse;
use Tests\AppBundle\BaseWebTestCase;

class FacebookConnectHandlerTest extends BaseWebTestCase
{
    const ACCESS_TOKEN = 'access_token';
    const PICTURE_PATH = 'user/picture.jpg';

    public function testHandleConnect()
    {
        $user = new User();
        $sut = $this->getSut($user);
        $actual = $sut->handleConnect(self::ACCESS_TOKEN);
        $this->assertInstanceOf(JsonResponse::class, $actual);
        $this->assertEquals('http://tbmedia2.imgix.net/'.self::PICTURE_PATH, $user->getAvatarFacebook());
    }

    public function testHandleConnectExistingUser()
    {
        $user = new User();
        $sut = $this->getSut($user, true);
        $actual = $sut->handleConnect(self::ACCESS_TOKEN);
        $this->assertInstanceOf(JsonResponse::class, $actual);
    }

    /**
     * @return FacebookConnectHandler
     */
    private function getSut($user, $existingUser = true): FacebookConnectHandler
    {
        /** @var APIResponseBuilder|PHPUnit_Framework_MockObject_MockObject $apiResponseBuilder */
        $apiResponseBuilder = $this->getMockBuilder(APIResponseBuilder::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var UserManager|PHPUnit_Framework_MockObject_MockObject $userManager */
        $userManager = $this->getMockBuilder(UserManager::class)
            ->disableOriginalConstructor()
            ->getMock();
        if ($existingUser === true) {
            $userManager->expects($this->any())
                ->method('findInAllUserBy')
                ->willReturn($user);
        }
        $userManager->expects($this->any())
            ->method('createUser')
            ->willReturn($user);

        /** @var JWTManager|PHPUnit_Framework_MockObject_MockObject $JWTManager */
        $JWTManager = $this->getMockBuilder(JWTManager::class)
            ->disableOriginalConstructor()
            ->getMock();

        /** @var FileUploader|PHPUnit_Framework_MockObject_MockObject $fileUploader */
        $fileUploader = $this->getMockBuilder(FileUploader::class)
            ->disableOriginalConstructor()
            ->getMock();
        $fileUploader->expects($this->any())
            ->method('upload')
            ->willReturn(self::PICTURE_PATH);

        $mockClient = $this->getMockBuilder(FacebookHttpClientInterface::class)
            ->disableOriginalConstructor()
            ->getMock();
        $mockClient->expects($this->any())
            ->method('send')
            ->will($this->returnCallback(function ($url) {
                switch ($this->extractTargetFromURL($url)) {
                    case 'me':
                        return $this->createResponse('{
  "location": {
    "id": "111175118906315",
    "name": "Berlin, Germany"
  },
  "email": "patrick@cynova.de",
  "first_name": "Patrick",
  "last_name": "Trost",
  "gender": "male",
  "id": "1538810309493156"
}');
                        break;
                    case 'me/picture':
                        return $this->createResponse('{
  "data": {
    "is_silhouette": false,
    "url": "'.realpath(__DIR__ . '/../../DataFixtures/Media/test.jpg').'"
  }
}');
                        break;
                    case '111175118906315':
                        return $this->createResponse('{
  "location": {
    "city": "Berlin",
    "country": "Germany",
    "latitude": 52.5006,
    "longitude": 13.3989
  },
  "name": "Berlin, Germany",
  "id": "111175118906315"
}');
                        break;
                    default:
                        throw new Exception('Unhandled URL: '.$url);

                        break;
                }
            }));
        $facebook = new Facebook([
            'http_client_handler' => $mockClient,
            'app_id' => 'app_id',
            'app_secret' => 'app_secret',
            'default_graph_version' => 'v2.8',
        ]);
        $facebookConnectHandler = new FacebookConnectHandler($apiResponseBuilder, $facebook, $userManager, $JWTManager, $fileUploader);

        return $facebookConnectHandler;
    }

    /**
     * @param string $body
     *
     * @return GraphRawResponse
     */
    private function createResponse(string $body): GraphRawResponse
    {
        $response = new GraphRawResponse([], $body);

        return $response;
    }

    /**
     * @param string $url
     *
     * @return string|null
     */
    private function extractTargetFromURL(string $url)
    {
        if (preg_match('/graph\.facebook\.com\/v[\d]\.[\d]\/([^\?]+)?/', $url, $match)) {
            return $match[1];
        }

        return null;
    }
}
