<?php

namespace AppBundle\Controller;

use AppBundle\Model\APIResponse;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\HttpFoundation\Request;

/**
 * RaceEvent controller.
 */
class RaceEventCompletedController extends Controller implements ClassResourceInterface
{
    /**
     * @SWG\Post(
     *     path="/raceevents/{id}/user/{userId}/completed",
     *     summary="Set a race event as completed ",
     *     description="Sets a race event as completed by a user and adds a vote.",
     *     tags={"Race Event"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     @SWG\Parameter(
     *         description="ID of the race event",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="ID of the user",
     *         in="path",
     *         name="userId",
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
     * @Post("/raceevents/{id}/user/{userId}/completed")
     *
     * @param string  $id
     * @param string  $userId
     * @param Request $request
     *
     * @return APIResponse
     */
    public function postAction(string $id, string $userId, Request $request)
    {
        $raceEventCompletedHandler = $this->get('app.handler.race_event_completed');

        return $raceEventCompletedHandler->handleCreateOrUpdate($id, $userId, $request->request->all());
    }

    /**
     * @SWG\Put(
     *     path="/raceevents/{id}/user/{userId}/completed",
     *     summary="Update the race event completed data",
     *     description="Updates the race event completed data.",
     *     tags={"Race Event"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     @SWG\Parameter(
     *         description="ID of the race event",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="ID of the user",
     *         in="path",
     *         name="userId",
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
     * @Put("/raceevents/{id}/user/{userId}/completed")
     *
     * @param string $id
     * @param string $userId
     * @param Request $request
     *
     * @return APIResponse
     */
    public function putAction(string $id, string $userId, Request $request)
    {
        $raceEventCompletedHandler = $this->get('app.handler.race_event_completed');

        return $raceEventCompletedHandler->handleCreateOrUpdate($id, $userId, $request->request->all());
    }

    /**
     * @SWG\Delete(
     *     path="/raceevents/{id}/user/{userId}/completed",
     *     summary="Delete the race event completed data",
     *     description="Deletes the race event completed data.",
     *     tags={"Race Event"},
     *     @SWG\Parameter(
     *         description="ID of the race event",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="ID of the user",
     *         in="path",
     *         name="userId",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(response=204, description="Successful operation"),
     *     @SWG\Response(response="404", description="RaceEvent or User not found"),
     * )
     *
     * @Delete("/raceevents/{id}/user/{userId}/completed")
     *
     * @param string $id
     * @param string $userId
     *
     * @return APIResponse
     */
    public function deleteAction(string $id, string $userId)
    {
        $raceEventCompletedHandler = $this->get('app.handler.race_event_completed');

        return $raceEventCompletedHandler->handleDelete($id, $userId);
    }
}
