<?php

namespace AppBundle\Controller;

use AppBundle\Model\APIResponse;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations\Put;

/**
 * Description.
 */
class ProfileController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/user",
     *     summary="Return the current user",
     *     description="Returns the current user.",
     *     tags={"User"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="Authorization", type="string", in="header", description="The authentication token", required=true),
     *     @SWG\Response(
     *         response=200,
     *         description="user created",
     *         @SWG\Schema(ref="#/definitions/User")
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Acecss Denied",
     *     )
     * )
     *
     * @Get("/user")
     *
     * @return APIResponse
     */
    public function getAction()
    {
        $apiResponseBuilder = $this->get('app.services.response_builder');
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        $response = $apiResponseBuilder->buildSuccessResponse($user, 'user');
        $response->addResponseGroup('user');

        return $response;
    }

    /**
     * @SWG\Get(
     *     path="/user/profile/{id}",
     *     summary="Find user by ID",
     *     description="Returns a single user.",
     *     tags={"User"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the user",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="int",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/User")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="User not found"
     *     ),
     * )
     *
     * @Get("/user/profile/{id}")
     *
     * @param $id
     * 
     * @return APIResponse
     */
    public function getByIdAction($id)
    {
        $apiResponseBuilder = $this->get('app.services.response_builder');
        $userRepository = $this->get('app.user.repository');
        $user = $userRepository->findOneBy(['id' => $id]);
        if ($user === null) {
            $apiResponseBuilder->buildNotFoundResponse('User not found');
        }

        $response = $apiResponseBuilder->buildSuccessResponse($user, 'user');
            $response->addResponseGroup('user');

        return $response;
    }

    /**
     * @SWG\Put(
     *     path="/user",
     *     summary="Update the current user",
     *     description="Updates the current user.",
     *     tags={"User"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="Authorization", type="string", in="header", description="The authentication token", required=true),
     *     @SWG\Parameter(name="firstName", type="string", in="formData", description="The users first name", required=false),
     *     @SWG\Parameter(name="lastName", type="string", in="formData", description="The users last name", required=false),
     *     @SWG\Parameter(name="gender", type="integer", enum={"0", "1", "2"}, in="formData", description="0 = unspecified, 1 = female, 2 = male", required=false),
     *     @SWG\Parameter(name="coords", type="string", in="formData", description="The GPS coordinates of the users location", required=false),
     *     @SWG\Parameter(name="location", type="string", in="formData", description="The location of user, will be determined from 'coords' if 'location' is not set", required=false),
     *     @SWG\Parameter(name="social_media", type="string", in="formData", description="The social media URL' is not set", required=false),
     *     @SWG\Parameter(name="race_event_type", type="string", in="formData", description="The prefered type of the race event (road_run, trail_run)"),
     *     @SWG\Parameter(name="race_distance_max", type="integer", in="formData", description="The prefered race distance max value", required=false),
     *     @SWG\Parameter(name="race_distance_min", type="integer", in="formData", description="The prefered race distance min value", required=false),
     *     @SWG\Response(
     *         response=204,
     *         description="user updated",
     *     ),
     *     @SWG\Response(
     *         response=401,
     *         description="Acecss Denied",
     *     )
     * )
     *
     * @Put("/user")
     *
     * @param Request $request
     *
     * @return APIResponse
     */
    public function putAction(Request $request)
    {
        $apiResponseBuilder = $this->get('app.services.response_builder');
        $user = $this->getUser();

        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        $dispatcher = $this->get('event_dispatcher');

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_INITIALIZE, $event);

        $formFactory = $this->get('fos_user.profile.form.factory');

        $form = $formFactory->createForm(['method' => 'PUT']);
        $form->setData($user);
        $form->submit($request->request->all(), false);

        if (!$form->isValid()) {
            return $apiResponseBuilder->buildFormErrorResponse($form);
        }

        if ($request->request->has('coords') && !$request->request->has('location')) {
            $mapboxAPI = $this->get('app.services.mapbox_api');
            $regionFeatures = $mapboxAPI->reverseGeocode($user->getCoords());
            $user->setLocation($mapboxAPI->getLocationNameFromFeatures($regionFeatures));
        }

        $userManager = $this->get('fos_user.user_manager');

        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_SUCCESS, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $response = new Response();
        }

        $dispatcher->dispatch(FOSUserEvents::PROFILE_EDIT_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

        return $apiResponseBuilder->buildEmptyResponse(204);
    }

    /**
     * @SWG\Get(
     *     path="/user/latest",
     *     summary="Return the latest registered users",
     *     description="Returns the latest registered users with an avatar in descending order.",
     *     tags={"User"},
     *     consumes={"application/json"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Limit the results, default is 10",
     *         in="query",
     *         name="limit",
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="user created",
     *         @SWG\Schema(ref="#/definitions/User")
     *     ),
     * )
     *
     * @Get("/user/latest")
     *
     * @param Request $request
     *
     * @return APIResponse
     */
    public function getLatestAction(Request $request)
    {
        $apiResponseBuilder = $this->get('app.services.response_builder');
        $userRepository = $this->get('app.user.repository');
        $latestUsers = $userRepository->findLatestActiveWithAvatar($request->get('limit', 10));

        $response = $apiResponseBuilder->buildSuccessResponse($latestUsers, 'user');
        $response->addResponseGroup('user');

        return $response;
    }
}
