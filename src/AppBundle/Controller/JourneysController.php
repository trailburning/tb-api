<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Routing\ClassResourceInterface;

class JourneysController extends Controller implements ClassResourceInterface
{
    /**
     * @SWG\Get(
     *     path="/journeys/{id}",
     *     summary="Find a journey by ID",
     *     description="Returns a single journey.",
     *     tags={"Journey"},
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
     *         description="successful operation",
     *         @SWG\Schema(ref="#/definitions/Journey")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="journey not found"
     *     ),
     * )
     *
     * @return APIResponse
     */
    public function getAction($id)
    {
        $journeyService = $this->get('tb.journey');
        
        return $journeyService->buildGetAPIResponse($id);
    }
}
