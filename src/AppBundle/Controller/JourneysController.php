<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Controller\Annotations\Delete;
use AppBundle\Form\Type\GPXImportType;
use AppBundle\Form\Type\JourneyType;
use AppBundle\Entity\Journey;

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
     *         description="ID of journey to return",
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
        $journeyService = $this->get('tb.journey');

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
        $journeyService = $this->get('tb.journey');

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
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Journey")
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
    public function importGPXAction($id)
    {
        $journeyRepository = $this->get('tb.journey.repository');
        $apiResponseBuilder = $this->get('tb.response.builder');

        $journey = $journeyRepository->findOneBy([
            'oid' => $id,
        ]);

        if ($journey === null) {
            $apiResponseBuilder->buildNotFoundResponse('Journey not found');
        }

        $form = $this->createForm(new GPXImportType());
        $form->handleRequest($this->getRequest());

        if (!$form->isValid()) {
            $apiResponseBuilder->buildResponse(400, 'Invalid or empty GPX file.');
        }

        $file = $form->get('file')->getData();
        $journeyService = $this->get('tb.journey');

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
        $journeyService = $this->get('tb.journey');

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
     *     @SWG\Parameter(name="position", type="integer", in="formData", description="The sort position"),
     *     @SWG\Parameter(name="publish", type="boolean", in="formData", description="Publish this journey"),
     *     @SWG\Response(response=201, description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Journey")
     *     ),
     *     @SWG\Response(response="400", description="Invalid data."),
     * )
     *
     * @return APIResponse
     */
    public function postAction()
    {
        return $this->processForm(new Journey(), $this->getRequest()->request->all(), $this->getRequest()->getMethod());
    }

    /**
     * @SWG\Put(
     *     path="/journeys/{id}",
     *     summary="Update a journey",
     *     description="Updates a journey.",
     *     tags={"Journeys"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", type="string", in="path", description="ID of journey to update", required=true),
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
    public function putAction($id)
    {
        $journeyRepository = $this->get('tb.journey.repository');

        $journey = $journeyRepository->findOneBy([
            'oid' => $id,
        ]);

        if ($journey === null) {
            $apiResponseBuilder->buildNotFoundResponse('Journey not found');
        }

        return $this->processForm($journey, $this->getRequest()->request->all(), $this->getRequest()->getMethod());
    }

    /**
     * @param Journey $journey
     * @param array   $parameters
     * @param string  $method
     *
     * @return APIResponse
     */
    private function processForm(Journey $journey, array $parameters, $method = 'PUT')
    {
        $apiResponseBuilder = $this->get('tb.response.builder');
        $form = $this->createForm(new JourneyType(), $journey, ['method' => $method]);
        $form->submit($parameters, 'PUT' !== $method);
        if (!$form->isValid()) {
            return $apiResponseBuilder->buildFormErrorResponse($form);
        }
        $journeyService = $this->get('tb.journey');

        return $journeyService->createFromAPI($form->getData());
    }
}
