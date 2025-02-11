<?php

namespace AppBundle\Controller;

use AppBundle\Model\APIResponse;
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
     *         name="dateFrom",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Filter results by date to (yyyy-MM-dd)",
     *         in="query",
     *         name="dateTo",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Filter results by distance from in metres",
     *         in="query",
     *         name="distanceFrom",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Filter results by distance to in metres",
     *         in="query",
     *         name="distanceTo",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Filter results by GeoData Point in the format '(LNG, LAT)'",
     *         in="query",
     *         name="coords",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Sets a distance in metres to filter results for the 'coords' parameter",
     *         in="query",
     *         name="distance",
     *         type="integer",
     *     ),
     *     @SWG\Parameter(
     *         description="Filter the search results by type. Valid options are: 'road_run', 'trail_run'.",
     *         in="query",
     *         name="type",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Filter the search results by category. Valid options are: 'marathon', 'half_marathon', '10k', '5k'.",
     *         in="query",
     *         name="category",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Filter the search results by attribute ID, separate multiple attributes with comma.",
     *         in="query",
     *         name="attributes",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Sort the search results. Valid options are: 'relevance', 'distance' and 'date'.",
     *         in="query",
     *         name="sort",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Sort order direction. Valid options are: 'asc' and 'desc'.",
     *         in="query",
     *         name="order",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Limit the search results, default is unlimited",
     *         in="query",
     *         name="limit",
     *         type="string",
     *     ),
     *     @SWG\Parameter(
     *         description="Offset the search results, default value is 0.",
     *         in="query",
     *         name="offset",
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation"
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

        return $searchHandler->handleSearch($request->query->all());
    }
}
