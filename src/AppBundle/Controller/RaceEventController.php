<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\HttpFoundation\Request;

/**
 * RaceEvent controller.
 */
class RaceEventController extends Controller implements ClassResourceInterface
{

    /**
     * @SWG\Get(
     *     path="/raceevents/{id}",
     *     summary="Find a race event by ID",
     *     description="Returns a single race event.",
     *     tags={"Race Event"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the race event to return",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/RaceEvent")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="RaceEvent not found"
     *     ),
     * )
     *
     * @Get("/raceevents/{id}")
     *
     * @param string $id
     *
     * @return APIResponse
     */
    public function getAction($id)
    {
        $raceEventHandler = $this->get('app.handler.race_event');

        return $raceEventHandler->handleGet($id);
    }

    /**
     * @SWG\Get(
     *     path="/raceevents",
     *     summary="Find race event",
     *     description="Returns all race events.",
     *     tags={"Race Event"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/RaceEvent")
     *     )
     * )
     *
     * @Get("/raceevents")
     *
     * @return APIResponse
     */
    public function getListAction()
    {
        $raceEventHandler = $this->get('app.handler.race_event');

        return $raceEventHandler->handleGetList();
    }

    /**
     * @SWG\Post(
     *     path="/raceevents",
     *     summary="Add a race event",
     *     description="Adds a race event.",
     *     tags={"Race Event"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="name", type="string", in="formData", description="The name of the race event", required="true"),
     *     @SWG\Parameter(name="about", type="string", in="formData", description="About the race event"),
     *     @SWG\Parameter(name="website", type="string", in="formData", description="The website of the race event"),
     *     @SWG\Parameter(name="email", type="string", in="formData", description="The contact email of the race event"),
     *     @SWG\Parameter(name="coords", type="string", in="formData", description="The GPS coordinates of the race event in the format '(LNG, LAT)'", required="true"),
     *     @SWG\Parameter(name="location", type="string", in="formData", description="The location of the race event, will be determined from 'coords' if 'location' is not set", required=false),
     *     @SWG\Parameter(name="type", type="string", in="formData", description="The type of the race event (road_run, trai_rRun)"),
     *     @SWG\Response(response=201, description="Successful operation. The Location header contains a link to the new race event.",
     *        @SWG\Header(header="location", type="string", description="Link to the new race event.")),
     *     @SWG\Response(response="400", description="Invalid data."),
     * )
     *
     * @Post("/raceevents")
     *
     * @param Request $request
     * @return APIResponse
     */
    public function postAction(Request $request)
    {
        $raceEventHandler = $this->get('app.handler.race_event');

        return $raceEventHandler->handleCreateOrUpdate($request->request->all());;
    }

    /**
     * @SWG\Put(
     *     path="/raceevents/{id}",
     *     summary="Update a race event",
     *     description="Updates a race event.",
     *     tags={"Race Event"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},  
     *     @SWG\Parameter(name="id", type="string", in="path", description="The ID of the race event", required=true),  
     *     @SWG\Parameter(name="name", type="string", in="formData", description="The name of the race event"),
     *     @SWG\Parameter(name="about", type="string", in="formData", description="About the race event"),
     *     @SWG\Parameter(name="website", type="string", in="formData", description="The website of the race event"),
     *     @SWG\Parameter(name="email", type="string", in="formData", description="The contact email of the race event"),
     *     @SWG\Parameter(name="coords", type="string", in="formData", description="The GPS coordinates of the race event in the format '(LNG, LAT)'"),
     *     @SWG\Parameter(name="location", type="string", in="formData", description="The location of the race event, will be determined from 'coords' if 'location' is not set", required=false),
     *     @SWG\Parameter(name="type", type="string", in="formData", description="The type of the race event (road_run, trail_run)"), 
     *     @SWG\Response(response=204, description="Successful operation"),
     *     @SWG\Response(response="400", description="Invalid data."),
     * )
     *
     * @Put("/raceevents/{id}")
     *
     * @param int $id
     * @param Request $request
     * @return APIResponse
     */
    public function putAction($id, Request $request)
    {
        $apiResponseBuilder = $this->get('app.services.response_builder');
        $raceEventRepository = $this->get('app.repository.race_event');
        $raceEventHandler = $this->get('app.handler.race_event');

        $raceEvent = $raceEventRepository->findOneBy([
            'oid' => $id,
        ]);
        
        if ($raceEvent === null) {
            return $apiResponseBuilder->buildNotFoundResponse('RaceEvent not found');
        }

        return $raceEventHandler->handleCreateOrUpdate(
            $request->request->all(),
            $raceEvent,
            $request->getMethod()
        );
    }

    /**
     * @SWG\Delete(
     *     path="/raceevents/{id}",
     *     summary="Delete a race event",
     *     description="Deletes the race event.",
     *     tags={"Race Event"},
     *     @SWG\Parameter(name="id", type="string", in="path", description="ID of the race event", required=true),
     *     @SWG\Response(response=204, description="Successful operation"),
     *     @SWG\Response(response="404", description="RaceEvent not found"), 
     * )
     *
     * @Delete("/raceevents/{id}")
     *
     * @param int $id
     *
     * @return APIResponse
     */
    public function deleteAction($id)
    {
        $raceEventHandler = $this->get('app.handler.race_event');

        return $raceEventHandler->handleDelete($id);
    }
}
