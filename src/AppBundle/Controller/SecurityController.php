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

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use FOS\RestBundle\Controller\Annotations\Post;
use Swagger\Annotations as SWG;

class SecurityController extends Controller
{
    
    /**
     * @SWG\Post(
     *     path="/user/login",
     *     summary="",
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
     *
     * @param Request $request
     *
     * @return APIResponse
     */
    public function loginAction(Request $request)
    {
        throw new \DomainException('You should never see this');
    }

}
