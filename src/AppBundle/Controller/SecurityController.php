<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace AppBundle\Controller;

use AppBundle\Model\APIResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\Post;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SecurityController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/user/login",
     *     summary="User login",
     *     description="Authenticates a user",
     *     tags={"User"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="username", type="string", in="formData", description="The email address of the user", required="true"),
     *     @SWG\Parameter(name="password", type="string", format="password", in="formData", description="The password", required="true"),
     *     @SWG\Response(
     *         response=200,
     *         description="Authentification successful",
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Invalid credentials",
     *     )
     * )
     *
     * @Post("/user/login")
     */
    public function loginAction()
    {
        throw new \DomainException('You should never see this');
    }

    /**
     * @SWG\Post(
     *     path="/user/connect",
     *     summary="Facebook connect",
     *     description="Connects a Facebook user",
     *     tags={"User"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="access_token", type="string", in="formData", description="The Facebook access token", required="true"),
     *     @SWG\Response(
     *         response=200,
     *         description="Authentification successful",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="Invalid access_token"
     *     )
     * )
     *
     * @Post("/user/connect")
     *
     * @param Request $request
     *
     * @return APIResponse|JsonResponse
     */
    public function connectAction(Request $request)
    {
        $facebookConnectHnndler = $this->get('app.handler.facebook_connect');

        return $facebookConnectHnndler->handleConnect($request->request->get('access_token'));
    }
}
