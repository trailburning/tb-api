<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

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
     *         type="string",
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
     * @param string $id
     *
     * @return APIResponse
     */
    public function getByJourneyAction($id)
    {
        $eventService = $this->get('app.event');

        return $eventService->buildGetByJourneyAPIResponse($id);
    }

    /**
     * @SWG\Post(
     *     path="/journeys/{id}/events",
     *     summary="Add an event",
     *     description="Adds an event.",
     *     tags={"Events"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="id", type="string", in="path", description="ID of the journey the event belongs to", required=true),
     *     @SWG\Parameter(name="name", type="string", in="formData", description="The name of the event", required=true),
     *     @SWG\Parameter(name="about", type="string", in="formData", description="About the event", required=true),
     *     @SWG\Parameter(name="coords", type="string", in="formData", description="The GPS coordinates of the event in the format '(LNG, LAT)'", required=true),
     *     @SWG\Parameter(name="position", type="integer", in="formData", description="The sort position, is handled automatically if not specified"),
     *     @SWG\Parameter(name="custom[0][key]", type="string", in="formData", description="The name of the event"),
     *     @SWG\Parameter(name="custom[0][value]", type="string", in="formData", description="The name of the event"),
     *     @SWG\Parameter(name="custom[1][key]", type="string", in="formData", description="The name of the event"),
     *     @SWG\Parameter(name="custom[1][value]", type="string", in="formData", description="The name of the event"),
     *     @SWG\Response(response=201, description="Successful operation. The Location header contains a link to the new event.",
     *        @SWG\Header(header="location", type="string", description="Link to the new event.")),
     *     @SWG\Response(response="400", description="Invalid data."),
     * )
     *
     * @Post("/journeys/{id}/events")
     *
     * @return APIResponse
     */
    public function postAction(Request $request, $id)
    {
        $apiResponseBuilder = $this->get('app.response.builder');
        $journeyRepository = $this->get('app.journey.repository');

        $journey = $journeyRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($journey === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Journey not found');
        }

        $eventService = $this->get('app.event');
        $request->request->set('journey', $journey->getId());

        return $eventService->createOrUpdateFromAPI($request->request->all());
    }

    /**
     * @SWG\Put(
     *     path="/events/{id}",
     *     summary="Update an event",
     *     description="Updates a event.",
     *     tags={"Events"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", type="string", in="path", description="ID of the event to update", required=true),
     *     @SWG\Parameter(name="name", type="string", in="formData", description="The name of the event"),
     *     @SWG\Parameter(name="about", type="string", in="formData", description="About the event"),
     *     @SWG\Parameter(name="position", type="integer", in="formData", description="The sort position, is handled automatically if not specified"),
     *     @SWG\Parameter(name="coords", type="string", in="formData", description="The GPS coordinates of the event"),
     *     @SWG\Response(response=204, description="Successful operation"),
     *     @SWG\Response(response="400", description="Invalid data."),
     * )
     *
     * @param string $id
     *
     * @return APIResponse
     */
    public function putAction(Request $request, $id)
    {
        $apiResponseBuilder = $this->get('app.response.builder');
        $eventRepository = $this->get('app.event.repository');
        $eventService = $this->get('app.event');

        $event = $eventRepository->findOneBy([
            'oid' => $id,
        ]);

        if ($event === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Journey not found');
        }

        return $eventService->createOrUpdateFromAPI(
            $request->request->all(),
            $event,
            $request->getMethod()
        );
    }

    /**
     * @SWG\Delete(
     *     path="/events/{id}",
     *     summary="Delete an event",
     *     description="Deletes the event.",
     *     tags={"Events"},
     *     @SWG\Parameter(name="id", type="string", in="path", description="ID of the event", required=true),
     *     @SWG\Response(response=204, description="Successful operation"),
     *     @SWG\Response(response="404", description="Journey not found"),
     * )
     *
     * @Delete("/events/{id}")
     *
     * @param int $id
     *
     * @return APIResponse
     */
    public function deleteAction($id)
    {
        $eventService = $this->get('app.event');

        return $eventService->deleteFromAPI($id);
    }

    /**
     * @SWG\Get(
     *     path="/events/{id}",
     *     summary="Find an event by ID",
     *     description="Returns a single event.",
     *     tags={"Events"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the event to return",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Event")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Event not found"
     *     ),
     * )
     *
     * @param string $id
     *
     * @return APIResponse
     */
    public function getAction($id)
    {
        $eventService = $this->get('app.event');

        return $eventService->buildGetAPIResponse($id);
    }
}
