<?php

namespace AppBundle\Controller;

use AppBundle\Model\APIResponse;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * RaceEvent controller.
 */
class RaceEventCompletedController extends Controller implements ClassResourceInterface
{
    /**
     * @SWG\Post(
     *     path="/user/raceevents/{id}/completed",
     *     summary="Set a race event as completed ",
     *     description="Sets a race event as completed by a user and adds a vote.",
     *     tags={"User"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="Authorization", type="string", in="header", description="The authentication token", required=true),
     *     @SWG\Parameter(
     *         description="ID of the race event",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(name="rating", type="int", in="formData", description="A rating between 1 and 5"),
     *     @SWG\Parameter(name="comment", type="string", in="formData", description="A comment about the race event"),
     *     @SWG\Response(response=201, description="Successful operation."),
     *     @SWG\Response(response="404", description="RaceEvent or User not found."),
     *     @SWG\Response(response="400", description="Invalid data."),
     * )
     *
     * @Post("/user/raceevents/{id}/completed")
     *
     * @param string  $id
     * @param Request $request
     *
     * @return APIResponse
     */
    public function postAction(string $id, Request $request)
    {
        $raceEventCompletedHandler = $this->get('app.handler.race_event_completed');
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        return $raceEventCompletedHandler->handleCreateOrUpdate($id, $user->getId(), $request->request->all());
    }

    /**
     * @SWG\Put(
     *     path="/user/raceevents/{id}/completed",
     *     summary="Update the race event completed data",
     *     description="Updates the race event completed data.",
     *     tags={"User"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="Authorization", type="string", in="header", description="The authentication token", required=true),
     *     @SWG\Parameter(
     *         description="ID of the race event",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(name="rating", type="int", in="formData", description="A rating between 1 and 5"),
     *     @SWG\Parameter(name="comment", type="string", in="formData", description="A comment about the race event"),
     *     @SWG\Response(response=204, description="Successful operation."),
     *     @SWG\Response(response="404", description="RaceEvent or User not found."),
     *     @SWG\Response(response="400", description="Invalid data."),
     * )
     *
     * @Put("/user/raceevents/{id}/completed")
     *
     * @param string $id
     * @param Request $request
     *
     * @return APIResponse
     */
    public function putAction(string $id, Request $request)
    {
        $raceEventCompletedHandler = $this->get('app.handler.race_event_completed');
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        return $raceEventCompletedHandler->handleCreateOrUpdate($id, $user->getId(), $request->request->all());
    }

    /**
     * @SWG\Delete(
     *     path="/user/raceevents/{id}/completed",
     *     summary="Delete the race event completed data",
     *     description="Deletes the race event completed data.",
     *     tags={"User"},
     *     @SWG\Parameter(name="Authorization", type="string", in="header", description="The authentication token", required=true),
     *     @SWG\Parameter(
     *         description="ID of the race event",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(response=204, description="Successful operation"),
     *     @SWG\Response(response="404", description="RaceEvent or User not found"),
     * )
     *
     * @Delete("/user/raceevents/{id}/completed")
     *
     * @param string $id
     *
     * @return APIResponse
     */
    public function deleteAction(string $id)
    {
        $raceEventCompletedHandler = $this->get('app.handler.race_event_completed');
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        return $raceEventCompletedHandler->handleDelete($id, $user->getId());
    }
}
