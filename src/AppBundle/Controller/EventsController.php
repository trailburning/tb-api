<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Get;

class EventsController extends Controller implements ClassResourceInterface
{
    /**
     * @SWG\Get(
     *     path="/journeys/{id}/events",
     *     summary="Find events by journey",
     *     description="Returns all events of a journey.",
     *     tags={"Events"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the journey",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer",
     *         format="int32"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Event")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Journey not found"
     *     ),
     * )
     *
     * @Get("/journeys/{id}/events")
     *
     * @param int $id
     *
     * @return APIResponse
     */
    public function getByJourneyAction($id)
    {
        $eventService = $this->get('tb.event');
        
        return $eventService->buildGetByJourneyAPIResponse($id);
    }
}
