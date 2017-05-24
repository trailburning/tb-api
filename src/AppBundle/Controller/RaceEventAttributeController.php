<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Put;
use FOS\RestBundle\Controller\Annotations\Delete;
use Symfony\Component\HttpFoundation\Request;

/**
     * RaceEvent controller.
     */
    class RaceEventAttributeController extends Controller implements ClassResourceInterface
    {
        /**
         * @SWG\Get(
         *     path="/raceevents/attributes",
         *     summary="Get all race event attributes",
         *     description="Returns all race event attributes.",
         *     tags={"Race Event"},
         *     produces={"application/json"},
         *     @SWG\Response(
         *         response=200,
         *         description="Successful operation",
         *         @SWG\Schema(ref="#/definitions/RaceEventAttribute")
         *     )
         * )
         *
         * @Get("/raceevents/attributes")
         *
         * @return APIResponse
         */
        public function getListAction()
        {
            $raceEventAttributeHandler = $this->get('app.handler.race_event_attribute');

            return $raceEventAttributeHandler->handleGetList();
        }
        
        /**
         * @SWG\Get(
         *     path="/raceevents/{id}/attributes",
         *     summary="Get all attributes of a race event ",
         *     description="Returns all attributes of a race event.",
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
         *         @SWG\Schema(ref="#/definitions/RaceEventAttribute")
         *     )
         * )
         *
         * @Get("/raceevents/{id}/attributes")
         *
         * @param string $id
         *
         * @return APIResponse
         */
        public function getRaceEventListAction($id)
        {
            $raceEventAttributeHandler = $this->get('app.handler.race_event_attribute');

            return $raceEventAttributeHandler->handleGetRaceEventList($id);
        }

        /**
         * @SWG\Put(
         *     path="/raceevents/{id}/attributes/{attributeId}",
         *     summary="Add an attribute to a race event",
         *     description="Adds an attribute to a race event.",
         *     tags={"Race Event"},
         *     consumes={"application/json","application/x-www-form-urlencoded"},  
         *     @SWG\Parameter(name="id", type="string", in="path", description="The ID of the race event", required=true),  
         *     @SWG\Parameter(name="attributeId", type="string", in="path", description="The ID of the race event attribute", required=true),
         *     @SWG\Response(response=204, description="Successful operation"),
         *     @SWG\Response(response="400", description="Invalid data."),
         * )
         *
         * @Put("/raceevents/{id}/attributes/{attributeId}")
         *
         * @param int     $id
         * @param int     $attributeId
         * @param Request $request
         *
         * @return APIResponse
         */
        public function putAction($id, $attributeId)
        {
            $apiResponseBuilder = $this->get('app.services.response_builder');
            $raceEventRepository = $this->get('app.repository.race_event');
            $raceEventAttributeHandler = $this->get('app.handler.race_event_attribute');

            return $raceEventAttributeHandler->handleAdd(
                $id,
                $attributeId
            );
        }

        /**
         * @SWG\Delete(
         *     path="/raceevents/{id}/attributes/{attributeId}",
         *     summary="Remove an attribute from a race event",
         *     description="Removes an attribute from a race event.",
         *     tags={"Race Event"},
         *     @SWG\Parameter(name="id", type="string", in="path", description="ID of the race event", required=true),
         *     @SWG\Parameter(name="attributeId", type="string", in="path", description="The ID of the race event attribute", required=true),
         *     @SWG\Response(response=204, description="Successful operation"),
         *     @SWG\Response(response="404", description="RaceEvent not found"), 
         * )
         *
         * @Delete("/raceevents/{id}/attributes/{attributeId}")
         *
         * @param int $id
         * @param int $attributeId
         *
         * @return APIResponse
         */
        public function deleteAction($id, $attributeId)
        {
            $raceEventAttributeHandler = $this->get('app.handler.race_event_attribute');

            return $raceEventAttributeHandler->handleRemove($id, $attributeId);
        }
    }
