<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AssetsController extends Controller implements ClassResourceInterface
{
    /**
     * @SWG\Get(
     *     path="/events/{id}/assets",
     *     summary="Find assets by event",
     *     description="Returns all assets of a event.",
     *     tags={"Assets"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the event",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Asset")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Event not found"
     *     ),
     * )
     *
     * @Get("/events/{id}/assets")
     *
     * @param string $id
     *
     * @return APIResponse
     */
    public function getByEventAction($id)
    {
        $assetService = $this->get('tb.asset');

        return $assetService->buildGetByEventAPIResponse($id);
    }

    /**
     * @SWG\Post(
     *     path="/events/{id}/assets",
     *     summary="Add an asset",
     *     description="Adds an asset fo an event.",
     *     tags={"Assets"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     produces={"application/json"},
     *     @SWG\Parameter(name="id", type="string", in="path", description="ID of the event the asset belongs to", required=true),
     *     @SWG\Parameter(name="name", type="string", in="formData", description="The name of the asset"),
     *     @SWG\Parameter(name="about", type="string", in="formData", description="About the asset"),
     *     @SWG\Parameter(name="category", type="string", in="formData", description="The asset category ID"),
     *     @SWG\Parameter(name="position", type="integer", in="formData", description="The sort position, is handled automatically if not specified"),
     *     @SWG\Parameter(name="credit", type="string", in="formData", description="The credit of the asset"),
     *     @SWG\Response(response=201, description="Successful operation. The Location header contains a link to the new event.",
     *        @SWG\Header(header="location", type="string", description="Link to the new event.")),
     *     @SWG\Response(response="400", description="Invalid data."),
     * )
     *
     * @Post("/events/{id}/assets")
     *
     * @return APIResponse
     */
    public function postAction(Request $request, $id)
    {
        $apiResponseBuilder = $this->get('tb.response.builder');
        $eventRepository = $this->get('tb.event.repository');

        $event = $eventRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($event === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Event not found');
        }

        $assetService = $this->get('tb.asset');
        $request->request->set('event', $event->getId());

        return $assetService->createOrUpdateFromAPI($request->request->all());
    }

    /**
     * @SWG\Put(
     *     path="/assets/{id}",
     *     summary="Update an asset",
     *     description="Updates an asset.",
     *     tags={"Assets"},
     *     consumes={"application/json","application/x-www-form-urlencoded"},
     *     @SWG\Parameter(name="id", type="string", in="path", description="ID of the asset to update", required=true),
     *     @SWG\Parameter(name="name", type="string", in="formData", description="The name of the asset"),
     *     @SWG\Parameter(name="about", type="string", in="formData", description="About the asset"),
     *     @SWG\Parameter(name="category", type="string", in="formData", description="The label of the asset category"),
     *     @SWG\Parameter(name="position", type="integer", in="formData", description="The sort position, is handled automatically if not specified"),
     *     @SWG\Parameter(name="credit", type="string", in="formData", description="The credit of the asset"),
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
        $apiResponseBuilder = $this->get('tb.response.builder');
        $assetRepository = $this->get('tb.asset.repository');
        $assetService = $this->get('tb.asset');

        $asset = $assetRepository->findOneBy([
            'oid' => $id,
        ]);

        if ($asset === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Asset not found');
        }

        return $assetService->createOrUpdateFromAPI(
            $request->request->all(),
            $asset,
            $request->getMethod()
        );
    }

    /**
     * @SWG\Delete(
     *     path="/assets/{id}",
     *     summary="Delete an asset",
     *     description="Deletes the asset.",
     *     tags={"Assets"},
     *     @SWG\Parameter(name="id", type="string", in="path", description="ID of the asset", required=true),
     *     @SWG\Response(response=204, description="Successful operation"),
     *     @SWG\Response(response="404", description="Journey not found"),
     * )
     *
     * @Delete("/assets/{id}")
     *
     * @param int $id
     *
     * @return APIResponse
     */
    public function deleteAction($id)
    {
        $assetService = $this->get('tb.asset');

        return $assetService->deleteFromAPI($id);
    }

    /**
     * @SWG\Get(
     *     path="/assets/{id}",
     *     summary="Find an asset by ID",
     *     description="Returns a single asset.",
     *     tags={"Assets"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the asset to return",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string",
     *     ),
     *     @SWG\Response(
     *         response=200,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Asset")
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Asset not found"
     *     ),
     * )
     *
     * @param string $id
     *
     * @return APIResponse
     */
    public function getAction($id)
    {
        $assetService = $this->get('tb.asset');

        return $assetService->buildGetAPIResponse($id);
    }
}
