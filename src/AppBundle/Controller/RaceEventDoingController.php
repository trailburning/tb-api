<?php

namespace AppBundle\Controller;

use AppBundle\Model\APIResponse;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * RaceEventDoing controller.
 */
class RaceEventDoingController extends Controller implements ClassResourceInterface
{
    /**
     * @SWG\Post(
     *     path="/user/raceevents/{id}/doing",
     *     summary="Add a race event to the doing list",
     *     description="Adds a race event to the doing list of a user.",
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
     *     @SWG\Response(response=201, description="Successful operation."),
     *     @SWG\Response(response="404", description="RaceEvent not found."),
     *     @SWG\Response(response="400", description="Invalid data."),
     *     @SWG\Response(
     *         response=401,
     *         description="Acecss Denied",
     *     )
     * )
     *
     * @Post("/user/raceevents/{id}/doing")
     *
     * @param string  $id
     * @param string  $userId
     *
     * @return APIResponse
     */
    public function postAction(string $id)
    {
        $raceEventCompletedHandler = $this->get('app.handler.race_event_doing');
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        return $raceEventCompletedHandler->handleCreate($id, $user->getId());
    }

    /**
     * @SWG\Delete(
     *     path="/user/raceevents/{id}/doing",
     *     summary="Remove a race event from the doing list",
     *     description="Removes a race event from the doing list of a user.",
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
     *     @SWG\Response(response="404", description="RaceEvent not found"),
     *     @SWG\Response(
     *         response=401,
     *         description="Acecss Denied",
     *     )
     * )
     *
     * @Delete("/user/raceevents/{id}/doing")
     *
     * @param string $id
     * @param string $userId
     *
     * @return APIResponse
     */
    public function deleteAction(string $id)
    {
        $raceEventCompletedHandler = $this->get('app.handler.race_event_doing');
        $user = $this->getUser();
        if (!is_object($user) || !$user instanceof UserInterface) {
            throw new AccessDeniedException();
        }

        return $raceEventCompletedHandler->handleDelete($id, $user->getId());
    }
}
