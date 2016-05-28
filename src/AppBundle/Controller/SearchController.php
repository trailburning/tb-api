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
class SearchController extends Controller implements ClassResourceInterface
{
    /**
     * @SWG\Get(
     *     path="/search",
     *     summary="Search for events and races",
     *     description="Returns a single race event.",
     *     tags={"Search"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="The term to search for",
     *         in="query",
     *         name="q",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Filter results by date from (yyyy-MM-dd)",
     *         in="query",
     *         name="date_from",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Filter results by date to (yyyy-MM-dd)",
     *         in="query",
     *         name="date_to",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Filter results by GeoData Point in the format '(LNG, LAT)'",
     *         in="query",
     *         name="coords",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Sets a distance in meters to filter results for the 'coords' parameter",
     *         in="query",
     *         name="distance",
     *         type="integer",
     *     ),
     * )
     *
     * @Get("/search")
     *
     * @return APIResponse
     */
    public function searchAction(Request $request)
    {
        $searchHandler = $this->get('app.handler.search');

        return $searchHandler->handleSearch($request->query);
    }
}
