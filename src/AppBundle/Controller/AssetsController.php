<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Get;

class AssetsController extends Controller implements ClassResourceInterface
{
    /**
     * @SWG\Get(
     *     path="/events/{id}/assets",
     *     summary="Find assets by event",
     *     description="Returns all assets of a event.",
     *     tags={"Assets"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the event",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Asset")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Event not found"
     *     ),
     * )
     *
     * @Get("/events/{id}/assets")
     *
     * @param string $id
     *
     * @return APIResponse
     */
    public function getByEventAction($id)
    {
        $assetService = $this->get('tb.asset');
        
        return $assetService->buildGetByEventAPIResponse($id);
    }
}
