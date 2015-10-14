<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use AppBundle\Response\APIResponse;
use Swagger\Annotations as SWG;

class StatusController extends Controller
{
    /**
     * @SWG\Get(
     *     path="/",
     *     summary="",
     *     tags={"Status"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
     *
     * @return APIResponse
     */
    public function getAction()
    {
        return new APIResponse();
    }
}
