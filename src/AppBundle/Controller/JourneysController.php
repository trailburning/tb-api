<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Get;

class JourneysController extends Controller implements ClassResourceInterface
{
    /**
     * @SWG\Get(
     *     path="/journeys/{id}",
     *     summary="Find a journey by ID",
     *     description="Returns a single journey.",
     *     tags={"Journeys"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of journey to return",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer",
     *         format="int32"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Journey")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="journey not found"
     *     ),
     * )
     *
     * @param int $id
     *
     * @return APIResponse
     */
    public function getAction($id)
    {
        $journeyService = $this->get('tb.journey');

        return $journeyService->buildGetAPIResponse($id);
    }

    /**
     * @SWG\Get(
     *     path="/journeys/user/{id}",
     *     summary="Find journeys by user",
     *     description="Returns all journeys by a user.",
     *     tags={"Journeys"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the user",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="integer",
     *         format="int32"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Journey")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="User not found"
     *     ),
     * )
     *
     * @Get("/journeys/user/{id}")
     *
     * @param int $id
     *
     * @return APIResponse
     */
    public function getByUserAction($id)
    {
        $journeyService = $this->get('tb.journey');
        return $journeyService->buildGetByUserAPIResponse($id);
    }
}
