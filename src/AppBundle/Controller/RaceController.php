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
 * Race controller.
 */
class RaceController extends Controller implements ClassResourceInterface
{

    /**
     * @SWG\Get(
     *     path="/races/{id}",
     *     summary="Find a race by ID",
     *     description="Returns a single race.",
     *     tags={"Race"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the race to return",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Race")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Race not found"
     *     ),
     * )
     *
     * @Get("/races/{id}")
     *
     * @param string $id
     *
     * @return APIResponse
     */
    public function getAction($id)
    {
        $raceHandler = $this->get('app.handler.race');

        return $raceHandler->handleGet($id);
    }

    /**
     * @SWG\Get(
     *     path="/raceevents/{id}/races",
     *     summary="Find all races for a race event",
     *     description="Returns all races.",
     *     tags={"Race"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the race event",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Race")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Race event not found"
     *     ),
     * )
     *
     * @Get("/raceevents/{id}/races")
     *
     * @return APIResponse
     */
    public function getByEventRaceAction($id)
    {
        $raceHandler = $this->get('app.handler.race');
        $raceEventRepository = $this->get('app.repository.race_event');
        $apiResponseBuilder = $this->get('app.response.builder');
        
        $raceEvent = $raceEventRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($raceEvent === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Race event not found');
        }

        return $raceHandler->handleGetListFilteredByRaceEvent($raceEvent);
    }

    /**
     * @SWG\Post(
     *     path="/raceevents/{id}/races",
     *     summary="Add a race",
     *     description="Adds a race.",
     *     tags={"Race"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", type="string", in="path", required="true", description="The ID of the race event the race belongs to"),
     *     @SWG\Parameter(name="name", type="string", in="formData", required="true", description="The name of the race"),
     *     @SWG\Parameter(name="date", type="string", in="formData", required="true", description="The date of the race (yyyy-MM-dd)"),
     *     @SWG\Parameter(name="type", type="string", in="formData", required="true", description="The type of the race"),
     *     @SWG\Parameter(name="distance", type="string", in="formData", required="true", description="The distance of the race"),
     *     @SWG\Response(response=201, description="Successful operation. The Location header contains a link to the new race.",
     *        @SWG\Header(header="location", type="string", description="Link to the new race.")),
     *     @SWG\Response(response="400", description="Invalid data."),
     * )
     *
     * @Post("/raceevents/{id}/races")
     *
     * @param Request $request
     * @return APIResponse
     */
    public function postAction(Request $request, $id)
    {
        $raceHandler = $this->get('app.handler.race');
        $apiResponseBuilder = $this->get('app.response.builder');
        $raceEventRepository = $this->get('app.repository.race_event');
        
        $raceEvent = $raceEventRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($raceEvent === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Race event not found');
        }
        $request->request->set('raceEvent', $raceEvent->getId());

        return $raceHandler->handleCreateOrUpdate($request->request->all());;
    }

    /**
     * @SWG\Put(
     *     path="/races/{id}",
     *     summary="Update a race",
     *     description="Updates a race.",
     *     tags={"Race"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", type="string", in="path", description="The ID of the race", required="true"),  
     *     @SWG\Parameter(name="name", type="string", in="formData", description="The name of the race"),
     *     @SWG\Parameter(name="date", type="string", in="formData", description="The date of the race"),
     *     @SWG\Parameter(name="type", type="string", in="formData", description="The type of the race"),
     *     @SWG\Parameter(name="distance", type="string", in="formData", description="The distance of the race"), 
     *     @SWG\Response(response=204, description="Successful operation"),
     *     @SWG\Response(response="400", description="Invalid data."),
     * )
     *
     * @Put("/races/{id}")
     *
     * @param int $id
     * @param Request $request
     * @return APIResponse
     */
    public function putAction($id, Request $request)
    {
        $apiResponseBuilder = $this->get('app.services.response_builder');
        $raceRepository = $this->get('app.repository.race');
        $raceHandler = $this->get('app.handler.race');

        $race = $raceRepository->findOneBy([
            'oid' => $id,
        ]);
        
        if ($race === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Race not found');
        }

        return $raceHandler->handleCreateOrUpdate(
            $request->request->all(),
            $race,
            $request->getMethod()
        );
    }

    /**
     * @SWG\Delete(
     *     path="/races/{id}",
     *     summary="Delete a race",
     *     description="Deletes the race.",
     *     tags={"Race"},
     *     @SWG\Parameter(name="id", type="string", in="path", description="ID of the race", required=true),
     *     @SWG\Response(response=204, description="Successful operation"),
     *     @SWG\Response(response="404", description="Race not found"), 
     * )
     *
     * @Delete("/races/{id}")
     *
     * @param int $id
     *
     * @return APIResponse
     */
    public function deleteAction($id)
    {
        $raceHandler = $this->get('app.handler.race');

        return $raceHandler->handleDelete($id);
    }
}
