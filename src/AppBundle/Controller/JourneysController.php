<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Routing\ClassResourceInterface;

class JourneysController extends Controller implements ClassResourceInterface
{
    /**
     * @SWG\Get(
     *     path="/v2/journeys",
     *     summary="Returns a single Journey",
     *     tags={"Journey"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
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
