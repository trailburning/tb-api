<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\GPXImportType;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class JourneysController extends Controller implements ClassResourceInterface
{
    /**
     * @SWG\Get(
     *     path="/journeys/{id}",
     *     summary="Find a journey by ID",
     *     description="Returns a single journey.",
     *     tags={"Journeys"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the journey to return",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Journey")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="journey not found"
     *     ),
     * )
     *
     * @param string $id
     *
     * @return APIResponse
     */
    public function getAction($id)
    {
        $journeyService = $this->get('app.journey');

        return $journeyService->buildGetAPIResponse($id);
    }

    /**
     * @SWG\Get(
     *     path="/journeys/user/{id}",
     *     summary="Find journeys by user",
     *     description="Returns all journeys by a user.",
     *     tags={"Journeys"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the user",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Journey")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="User not found"
     *     ),
     * )
     *
     * @Get("/journeys/user/{id}")
     *
     * @param int $id
     *
     * @return APIResponse
     */
    public function getByUserAction($id)
    {
        $journeyService = $this->get('app.journey');

        return $journeyService->buildGetByUserAPIResponse($id);
    }

    /**
     * @SWG\Post(
     *     path="/journeys/{id}/import/gpx",
     *     summary="Import a GPX file",
     *     description="Imports a GPX file and adds the routes found in the GPX to a journey. Replaces all previous routes.",
     *     tags={"Journeys"},
     *     consumes={"multipart/form-data"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the journey",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="The GPX File to import",
     *         in="formData",
     *         name="file",
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Successful operation",
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Journey not found"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid or empty GPX file."
     *     ),
     * )
     *
     * @Post("/journeys/{id}/import/gpx")
     *
     * @param int $id
     *
     * @return APIResponse
     */
    public function importGPXAction(Request $request, $id)
    {
        $journeyRepository = $this->get('app.journey.repository');
        $apiResponseBuilder = $this->get('app.response.builder');

        $journey = $journeyRepository->findOneBy([
            'oid' => $id,
        ]);

        if ($journey === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Journey not found');
        }

        $form = $this->createForm(GPXImportType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $apiResponseBuilder->buildResponse(400, 'Invalid or empty GPX file.');
        }

        $file = $form->get('file')->getData();
        $journeyService = $this->get('app.journey');

        return $journeyService->importGPX($file, $journey);
    }

    /**
     * @SWG\Delete(
     *     path="/journeys/{id}/route_points",
     *     summary="Delete all route points",
     *     description="Deletes all route points of a journey.",
     *     tags={"Journeys"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the journey",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Journey")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Journey not found"
     *     ),
     * )
     *
     * @Delete("/journeys/{id}/route_points")
     *
     * @param int $id
     *
     * @return APIResponse
     */
    public function deleteRoutePointsAction($id)
    {
        $journeyService = $this->get('app.journey');

        return $journeyService->deleteJourneyRoutePoints($id);
    }

    /**
     * @SWG\Post(
     *     path="/journeys",
     *     summary="Add a journey",
     *     description="Adds a journey.",
     *     tags={"Journeys"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="name", type="string", in="formData", description="The name of the journey"),
     *     @SWG\Parameter(name="about", type="string", in="formData", description="About the journey"),
     *     @SWG\Parameter(name="user", type="string", in="formData", description="The ID of the user the journey belongs to"),
     *     @SWG\Parameter(name="position", type="integer", in="formData", description="The sort position, is handled automatically if not specified"),
     *     @SWG\Parameter(name="publish", type="boolean", in="formData", description="Publish this journey, default value is 'false'"),
     *     @SWG\Response(response=201, description="Successful operation. The Location header contains a link to the new journey.",
     *        @SWG\Header(header="location", type="string", description="Link to the new event.")),
     *     @SWG\Response(response="400", description="Invalid data."),
     * )
     *
     * @return APIResponse
     */
    public function postAction(Request $request)
    {
        $journeyService = $this->get('app.journey');

        return $journeyService->createOrUpdateFromAPI($request->request->all());
    }

    /**
     * @SWG\Put(
     *     path="/journeys/{id}",
     *     summary="Update a journey",
     *     description="Updates a journey.",
     *     tags={"Journeys"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", type="string", in="path", description="ID of the journey to update", required=true),
     *     @SWG\Parameter(name="name", type="string", in="formData", description="The name of the journey"),
     *     @SWG\Parameter(name="about", type="string", in="formData", description="About the journey"),
     *     @SWG\Parameter(name="user", type="string", in="formData", description="The ID of the user the journey belongs to"),
     *     @SWG\Parameter(name="position", type="integer", in="formData", description="The sort position"),
     *     @SWG\Parameter(name="publish", type="boolean", in="formData", description="Publish this journey"),
     *     @SWG\Response(response=204, description="Successful operation"),
     *     @SWG\Response(response="400", description="Invalid data."),
     * )
     *
     * @param string $id
     *
     * @return APIResponse
     */
    public function putAction(Request $request, $id)
    {
        $apiResponseBuilder = $this->get('app.response.builder');
        $journeyRepository = $this->get('app.journey.repository');
        $journeyService = $this->get('app.journey');

        $journey = $journeyRepository->findOneBy([
            'oid' => $id,
        ]);

        if ($journey === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Journey not found');
        }

        return $journeyService->createOrUpdateFromAPI(
            $request->request->all(),
            $journey,
            $request->getMethod()
        );
    }

    /**
     * @SWG\Delete(
     *     path="/journeys/{id}",
     *     summary="Delete a journey",
     *     description="Deletes the journey.",
     *     tags={"Journeys"},
     *     @SWG\Parameter(name="id", type="string", in="path", description="ID of the Journey", required=true),
     *     @SWG\Response(response=204, description="Successful operation"),
     *     @SWG\Response(response="404", description="Journey not found"),
     * )
     *
     * @Delete("/journeys/{id}")
     *
     * @param int $id
     *
     * @return APIResponse
     */
    public function deleteAction($id)
    {
        $journeyService = $this->get('app.journey');

        return $journeyService->deleteFromAPI($id);
    }
}
