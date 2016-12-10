<?php

namespace AppBundle\Controller;

use AppBundle\Model\APIResponse;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use FOS\RestBundle\Controller\Annotations\Get;

class StatusController extends Controller
{

    /**
     * @SWG\Get(
     *     path="/summary",
     *     summary="",
     *     tags={"Status"},
     *     produces={"application/json"},
     *     @SWG\Response(
     *         response=200,
     *         description="successful operation",
     *     )
     * )
    *
    * @Get("/summary")
     *
     * @return APIResponse
     */
    public function getSummaryAction()
    {
        $handler = $this->get('app.handler.summary');

        return $handler->handleGet();
    }
}
