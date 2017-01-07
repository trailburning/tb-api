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

use AppBundle\Entity\User;
use AppBundle\Model\APIResponse;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/user/register",
     *     summary="User registration",
     *     description="Registers a new user.",
     *     tags={"User"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="email", type="string", in="formData", description="The email address of the user", required="true"),
     *     @SWG\Parameter(name="plainPassword[first]", type="string", format="password", in="formData", description="The password", required="true"),
     *     @SWG\Parameter(name="plainPassword[second]", type="string", format="password", in="formData", description="The password check", required="true"),
     *     @SWG\Parameter(name="firstName", type="string", in="formData", description="The users first name", required="true"),
     *     @SWG\Parameter(name="lastName", type="string", in="formData", description="The users last name", required="true"),
     *     @SWG\Parameter(name="gender", type="integer", enum={"0", "1", "2"}, in="formData", description="0 = unspecified, 1 = female, 2 = male", required="true"),
     *     @SWG\Parameter(name="location", type="string", in="formData", description="The location of the race event, will be determined from 'coords' if 'location' is not set", required=false),
     *     @SWG\Parameter(name="social_media", type="string", in="formData", description="The social media URL' is not set", required=false),
     *     @SWG\Parameter(name="race_event_type", type="string", in="formData", description="The prefered type of the race event (road_run, trail_run)"),
     *     @SWG\Parameter(name="race_distance_max", type="integer", in="formData", description="The prefered race distance max value", required=false),
     *     @SWG\Parameter(name="race_distance_min", type="integer", in="formData", description="The prefered race distance min value", required=false),
     *     @SWG\Response(
     *         response=201,
     *         description="user created",
     *     )
     * )
     *
     * @Post("/user/register")
     *
     * @param Request $request
     *
     * @return APIResponse
     */
    public function registerAction(Request $request)
    {
        $apiResponseBuilder = $this->get('app.services.response_builder');
        $formFactory = $this->get('fos_user.registration.form.factory');
        $userManager = $this->get('fos_user.user_manager');
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->createUser();
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, $event);

        if (null !== $event->getResponse()) {
            /** @var APIResponse $response */
            $response = $event->getResponse();

            return $response;
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $apiResponseBuilder->buildFormErrorResponse($form);
        }

        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $response = new Response();
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

        return $apiResponseBuilder->buildEmptyResponse(201);
    }

    /**
     * @SWG\Get(
     *     path="/user/confirm/{token}",
     *     summary="Registration email confirmation",
     *     description="Confirms the user registration",
     *     tags={"User"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="The confirmation token",
     *         in="path",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="User confirmation su successful",
     *     ),
     *     @SWG\Response(
     *         response=404,
     *         description="No user found for token",
     *     )
     * )
     *
     * @Get("/user/confirm/{token}")
     *
     * @param Request $request
     * @param string $token
     *
     * @return JsonResponse
     */
    public function confirmAction(Request $request, string $token)
    {
        $apiResponseBuilder = $this->get('app.services.response_builder');
        $userManager = $this->get('app.security.user_manager');

        /** @var $user User */
        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            return $apiResponseBuilder->buildNotFoundResponse(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);
        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $response = new Response();
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        $jwtManager = $this->container->get('lexik_jwt_authentication.jwt_manager');

        return new JsonResponse(['token' => $jwtManager->create($user)]);


//        return $apiResponseBuilder->buildEmptyResponse(204);
    }
}
