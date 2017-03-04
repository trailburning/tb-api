<?php

namespace AppBundle\Handler;

use AppBundle\Entity\User;
use AppBundle\Model\APIResponse;
use AppBundle\Security\UserManager;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Services\FileUploader;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Facebook;
use Facebook\GraphNodes\GraphNode;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWTManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;

/**
 * Facebook Connect Handler.
 */
class FacebookConnectHandler
{
    /**
     * @var APIResponseBuilder
     */
    private $apiResponseBuilder;

    /**
     * @var Facebook
     */
    private $facebook;

    /**
     * @var UserManager
     */
    private $userManager;

    /**
     * @var JWTManager
     */
    private $JWTManager;

    /**
     * @var Fileuploader
     */
    private $fileUploader;

    /**
     * FacebookConnectHandler constructor.
     *
     * @param APIResponseBuilder $apiResponseBuilder
     * @param Facebook           $facebook
     * @param UserManager        $userManager
     * @param JWTManager         $JWTManager
     * @param FileUploader       $fileUploader
     */
    public function __construct(
        APIResponseBuilder $apiResponseBuilder,
        Facebook $facebook,
        UserManager $userManager,
        JWTManager $JWTManager,
        FileUploader $fileUploader
    ) {
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->facebook = $facebook;
        $this->userManager = $userManager;
        $this->JWTManager = $JWTManager;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @param $accessToken
     *
     * @return JsonResponse|APIResponse
     */
    public function handleConnect($accessToken)
    {
        try {
            $profile = $this->getProfile('me', $accessToken);
            $picture = $this->getPicture('me', $accessToken);
            $user = $this->getOrCreateUserFromProfile($profile, $accessToken);
            if ($user->getAvatar() === null && $picture->getField('is_silhouette') === false) {
                $user = $this->updateAvatar($user, $picture);
            }

            if ($profile->getField('location')) {
                $user = $this->updateLocation($user, $profile, $accessToken);
            }
        } catch (FacebookResponseException $e) {
            return $this->apiResponseBuilder->buildBadRequestResponse($e->getMessage());
        }


        $this->userManager->updateUser($user);
        $response = new JsonResponse([
            'token' => $this->JWTManager->create($user),
        ]);

        return $response;
    }

    /**
     * @param string $nodeId
     * @param string $accessToken
     *
     * @return GraphNode
     */
    protected function getProfile(string $nodeId, string $accessToken)
    {
        $resource = '/'.$nodeId;
        $params = [
            'fields' => 'id,first_name,last_name,email,location,gender',
        ];
        $profile = $this->getGraphNode($resource, $accessToken, $params);

        return $profile;
    }

    /**
     * @param string $nodeId
     * @param string $accessToken
     *
     * @return GraphNode
     */
    protected function getPicture(string $nodeId, string $accessToken)
    {
        $resource = '/'.$nodeId.'/picture';
        $params = [
            'type' => 'large',
            'redirect' => false,
        ];
        $picture = $this->getGraphNode($resource, $accessToken, $params);

        return $picture;
    }

    /**
     * @param string $nodeId
     * @param $accessToken
     *
     * @return GraphNode
     */
    protected function getPlace(string $nodeId, $accessToken): GraphNode
    {
        $resource = '/'.$nodeId;
        $params = [
            'fields' => 'location,name',
        ];
        $location = $this->getGraphNode($resource, $accessToken, $params);

        return $location;
    }

    /**
     * @param string $accessToken
     * @param string $resource
     * @param array  $params
     *
     * @return GraphNode
     */
    protected function getGraphNode(string $resource, string $accessToken, array $params = [])
    {
        $url = $this->appendParams($resource, $params);
        $request = $this->facebook->request('GET', $url, [], $accessToken);
        $response = $this->facebook->getClient()->sendRequest($request);
        $graphNode = $response->getGraphNode();

        return $graphNode;
    }

    /**
     * @param string $url
     * @param array  $params
     *
     * @return string
     */
    protected function appendParams(string $url, array $params = []): string
    {
        if (count($params) === 0) {
            return $url;
        }

        $urlParams = [];
        foreach ($params as $key => $value) {
            $urlParams[] = $key.'='.$value;
        }

        $url .= '?'.implode('&', $urlParams);

        return $url;
    }

    /**
     * @param GraphNode $profile
     * @param string    $accessToken
     *
     * @return User
     */
    protected function getOrCreateUserFromProfile(GraphNode $profile, string $accessToken): User
    {
        /** @var User $user */
        $user = $this->userManager->findInAllUserBy([
            'oauthId' => $profile->getField('id'),
        ]);
        if ($user === null) {
            $email = $profile->getField('email', $profile->getField('id').'@facebook.com');
            $user = $this->userManager->createUser();
            $user->setEmail($email);
            $user->setPassword(uniqid(null, true));
            $user->setEnabled(true);
        }
        $user->setClient('race_base');
        $user->setFirstName($profile->getField('first_name'));
        $user->setLastName($profile->getField('last_name'));
        $user->setGender(USER::GENDER_NONE);
        $user->setOauthService('facebook');
        $user->setOauthId($profile->getField('id'));
        $user->setOauthAccessToken($accessToken);
        switch ($profile->getField('gender')) {
            case 'male':
                $user->setGender(USER::GENDER_MALE);
                break;
            case 'female':
                $user->setGender(USER::GENDER_FEMALE);
                break;
            default:
                break;
        }

        return $user;
    }

    /**
     * @param User      $user
     * @param GraphNode $profile
     * @param $accessToken
     *
     * @return User
     */
    protected function updateLocation(User $user, GraphNode $profile, string $accessToken): User
    {
        $nodeId = $profile->getField('location')->getField('id');
        $place = $this->getPlace($nodeId, $accessToken);
        $coords = new Point(
            $place->getField('location')->getField('longitude'),
            $place->getField('location')->getField('latitude'),
            4326
        );
        $user->setCoords($coords);
        $user->setLocation($place->getField('name'));

        return $user;
    }

    /**
     * @param User      $user
     * @param GraphNode $picture
     *
     * @return User
     */
    protected function updateAvatar(User $user, GraphNode $picture): User
    {
        $file = new UploadedFile($picture->getField('url'), $picture->getField('url'), null, null, UPLOAD_ERR_NO_TMP_DIR);
        $path = $this->fileUploader->upload($file, 'user');
        $url = 'http://tbmedia2.imgix.net/'.$path;
        $user->setAvatar($url);

        return $user;
    }
}
