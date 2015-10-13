<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Response\ApiResponse;
use Swagger\Annotations as SWG;

class DefaultController extends Controller
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
     * @return ApiResponse
     */
    public function getAction()
    {
        return new ApiResponse();
    }
}
