<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use AppBundle\Response\ApiResponse;

class DefaultController extends Controller
{
    /**
     * @return ApiResponse
     */
    public function getAction()
    {
        return new ApiResponse();
    }
}
