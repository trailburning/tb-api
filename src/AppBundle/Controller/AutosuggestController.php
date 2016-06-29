<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use Symfony\Component\HttpFoundation\Request;

/**
 * RaceEvent controller.
 */
class AutosuggestController extends Controller implements ClassResourceInterface
{
    /**
     * @SWG\Get(
     *     path="/autosuggest",
     *     summary="Get suggestions for the search",
     *     description="Returns a list of suggestions.",
     *     tags={"Search"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="The term to search for",
     *         in="query",
     *         name="q",
     *         type="string",
     *     ),
     * )
     *
     * @Get("/autosuggest")
     *
     * @return APIResponse
     */
    public function getAction(Request $request)
    {
        $autosuggestHandler = $this->get('app.handler.autosuggest');

        return $autosuggestHandler->handleGet($request->query->all());
    }
}
