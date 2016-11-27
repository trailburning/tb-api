<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use Symfony\Component\HttpFoundation\Response;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * ResettingController.
 */
class ResettingController extends Controller
{
    /**
     * @SWG\Post(
     *     path="/user/password/request",
     *     summary="Request a password reset",
     *     description="Sends the user aa link to reset the password.",
     *     tags={"User"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="Requests",
     *         in="formData",
     *         name="email",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="reset email sent",
     *     ),
     *     @SWG\Response(
     *         response=400,
     *         description="unknown email address, password reset already requested",
     *     ),
     * )
     *
     * @Post("/user/password/request")
     *
     * @param Request $request
     *
     * @return APIResponse
     */
    public function requestAction(Request $request)
    {
        /** @var AppBundle\Services\APIResponseBuilder $apiResponseBuilder */
        $apiResponseBuilder = $this->get('app.services.response_builder');
        $user = $this->get('fos_user.user_manager')
            ->findUserByUsernameOrEmail($request->request->get('email'));

        if (null === $user) {
            return $apiResponseBuilder->buildBadRequestResponse('Unknown email address');
        }

        $ttl = $this->container->getParameter('fos_user.resetting.token_ttl');
        if ($user->isPasswordRequestNonExpired($ttl)) {
            return $apiResponseBuilder->buildBadRequestResponse('password already requested');
        }

        if (null === $user->getConfirmationToken()) {
            /** @var $tokenGenerator \FOS\UserBundle\Util\TokenGeneratorInterface */
            $tokenGenerator = $this->get('fos_user.util.token_generator');
            $user->setConfirmationToken($tokenGenerator->generateToken());
        }

        $this->get('fos_user.mailer')->sendResettingEmailMessage($user);
        $user->setPasswordRequestedAt(new \DateTime());
        $this->get('fos_user.user_manager')->updateUser($user);

        return $apiResponseBuilder->buildEmptyResponse(204);
    }

    /**
     * @SWG\Post(
     *     path="/user/password/reset/{token}",
     *     summary="Set a new password",
     *     description="Sets a new password.",
     *     tags={"User"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="The password request token",
     *         in="path",
     *         name="token",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(name="plainPassword[first]", type="string", format="password", in="formData", description="The password", required="true"),
     *     @SWG\Parameter(name="plainPassword[second]", type="string", format="password", in="formData", description="The password check", required="true"),
     *     @SWG\Response(
     *         response=204,
     *         description="password change successful",
     *     ),
     *     @SWG\Response(response="400", description="Invalid data."),
     * )
     *
     * @Post("/user/password/reset/{token}")
     *
     * @param Request $request
     *
     * @return APIResponse
     */
    public function resetAction(Request $request, $token)
    {
        /** @var AppBundle\Services\APIResponseBuilder $apiResponseBuilder */
        $apiResponseBuilder = $this->get('app.services.response_builder');
        /** @var $formFactory \FOS\UserBundle\Form\Factory\FactoryInterface */
        $formFactory = $this->get('fos_user.resetting.form.factory');
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');
        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(
                sprintf('The user with "confirmation token" does not exist for value "%s"', $token));
        }

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_INITIALIZE, $event);

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $apiResponseBuilder->buildFormErrorResponse($form);
        }

        $event = new FormEvent($form, $request);
        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_SUCCESS, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $response = new Response();
        }

        $dispatcher->dispatch(FOSUserEvents::RESETTING_RESET_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

        return $apiResponseBuilder->buildEmptyResponse(204);
    }
}
