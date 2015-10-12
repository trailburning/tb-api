<?php

namespace AppBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations as Rest;
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
