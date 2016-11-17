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

use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\GetResponseUserEvent;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use FOS\UserBundle\Model\UserInterface;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Controller\Annotations\Post;
use Symfony\Component\HttpFoundation\Response;

class RegistrationController extends Controller
{
    
    /**
     * @SWG\Post(
     *     path="/register",
     *     summary="",
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
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     *
     * @Post("/register")
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
            return $event->getResponse();
        }

        $form = $formFactory->createForm();
        $form->setData($user);

        $form->handleRequest($request);
        
        if (!$form->isValid()) {
            return $apiResponseBuilder->buildBadRequestResponse((string)$form->getErrors(true, true));
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
     * Tell the user to check his email provider
     */
    public function checkEmailAction()
    {
        $email = $this->get('session')->get('fos_user_send_confirmation_email/email');
        $this->get('session')->remove('fos_user_send_confirmation_email/email');
        $user = $this->get('fos_user.user_manager')->findUserByEmail($email);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with email "%s" does not exist', $email));
        }

        return $this->render('FOSUserBundle:Registration:checkEmail.html.twig', array(
            'user' => $user,
        ));
    }

    /**
     * Receive the confirmation token from user email provider, login the user
     */
    public function confirmAction(Request $request, $token)
    {
        /** @var $userManager \FOS\UserBundle\Model\UserManagerInterface */
        $userManager = $this->get('fos_user.user_manager');

        $user = $userManager->findUserByConfirmationToken($token);

        if (null === $user) {
            throw new NotFoundHttpException(sprintf('The user with confirmation token "%s" does not exist', $token));
        }

        /** @var $dispatcher \Symfony\Component\EventDispatcher\EventDispatcherInterface */
        $dispatcher = $this->get('event_dispatcher');

        $user->setConfirmationToken(null);
        $user->setEnabled(true);

        $event = new GetResponseUserEvent($user, $request);
        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRM, $event);

        $userManager->updateUser($user);

        if (null === $response = $event->getResponse()) {
            $url = $this->generateUrl('fos_user_registration_confirmed');
            $response = new RedirectResponse($url);
        }

        $dispatcher->dispatch(FOSUserEvents::REGISTRATION_CONFIRMED, new FilterUserResponseEvent($user, $request, $response));

        return $response;
    }

    /**
     * Tell the user his account is now confirmed
     */
    public function confirmedAction()
    {
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException('This user does not have access to this section.');
        }

        return $this->render('FOSUserBundle:Registration:confirmed.html.twig', array(
            'user' => $user,
            'targetUrl' => $this->getTargetUrlFromSession(),
        ));
    }

    private function getTargetUrlFromSession()
    {
        // Set the SecurityContext for Symfony <2.6
        if (interface_exists('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface')) {
            $tokenStorage = $this->get('security.token_storage');
        } else {
            $tokenStorage = $this->get('security.context');
        }

        $key = sprintf('_security.%s.target_path', $tokenStorage->getToken()->getProviderKey());

        if ($this->get('session')->has($key)) {
            return $this->get('session')->get($key);
        }
    }
}
