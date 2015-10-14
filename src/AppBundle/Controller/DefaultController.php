<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Response\APIResponse;
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
     * @return APIResponse
     */
    public function getAction()
    {
        return new APIResponse();
    }
}
