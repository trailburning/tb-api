<?php

namespace AppBundle\Controller;

use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use AppBundle\Model\APIResponse;

class RaceEventMediaController extends Controller implements ClassResourceInterface
{
    /**
     * @SWG\Post(
     *     path="/raceevents/{id}/media",
     *     summary="Add a medias to an race event",
     *     description="Adds a more media file to an race event.",
     *     tags={"Race Event"},
     *     consumes={"multipart/form-data"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the race event",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="The media File to upload",
     *         in="formData",
     *         name="media",
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Parameter(name="credit", type="string", in="formData", description="Credit text"),
     *     @SWG\Parameter(name="creditUrl", type="string", in="formData", description="Credit URL"),
     *     @SWG\Parameter(name="publish", type="boolean", in="formData", description="Publish this media, default value is 'false'"),
     *     @SWG\Response(
     *         response=201,
     *         description="Successful operation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="RaceEvent not found"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid MIME type. File size too large."
     *     ),
     * )
     *
     * @Post("/raceevents/{id}/media")
     *
     * @param int     $id
     * @param Request $request
     *
     * @return APIResponse
     */
    public function postAction(Request $request, $id)
    {
        $raceEventRepository = $this->get('app.repository.race_event');
        $apiResponseBuilder = $this->get('app.response.builder');
        $raceEventMediaHandler = $this->get('app.handler.race_event_media_handler');

        $raceEvent = $raceEventRepository->findOneBy([
            'oid' => $id,
        ]);

        if ($raceEvent === null) {
            return $apiResponseBuilder->buildNotFoundResponse('RaceEvent not found');
        }

        return $raceEventMediaHandler->handleCreateOrUpdate($request, $raceEvent);
    }

    /**
     * @SWG\Post(
     *     path="/raceevents/{id}/media/{mediaId}",
     *     summary="Updates the media of an race event",
     *     description="Updates the media of an race event.",
     *     tags={"Race Event"},
     *     consumes={"multipart/form-data"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the race event",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="ID of the media",
     *         in="path",
     *         name="mediaId",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="The media File to upload",
     *         in="formData",
     *         name="media",
     *         required=false,
     *         type="file"
     *     ),
     *     @SWG\Parameter(name="credit", type="string", in="formData", description="Credit text"),
     *     @SWG\Parameter(name="creditUrl", type="string", in="formData", description="Credit URL"),
     *     @SWG\Parameter(name="publish", type="boolean", in="formData", description="Publish this media media, default value is 'true'"),
     *     @SWG\Response(
     *         response=204,
     *         description="Successful operation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="RaceEvent not found. Media not found."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid MIME type. File size too large."
     *     ),
     * )
     *
     * @Post("/raceevents/{id}/media/{mediaId}")
     *
     * @param Request $request
     * @param int     $id
     * @param int     $mediaId
     *
     * @return APIResponse
     */
    public function putAction(Request $request, $id, $mediaId)
    {
        $raceEventMediaHandler = $this->get('app.handler.race_event_media_handler');
        $raceEventRepository = $this->get('app.repository.race_event');
        $mediaRepository = $this->get('app.media.repository');
        $apiResponseBuilder = $this->get('app.response.builder');

        $raceEvent = $raceEventRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($raceEvent === null) {
            return $apiResponseBuilder->buildNotFoundResponse('RaceEvent not found');
        }

        $media = $mediaRepository->findOneBy([
            'oid' => $mediaId,
        ]);
        if ($media === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Media not found');
        }

        return $raceEventMediaHandler->handleCreateOrUpdate($request, $raceEvent, $media);
    }

    /**
     * @SWG\Delete(
     *     path="/raceevents/{id}/media/{mediaId}",
     *     summary="Delete a media",
     *     description="Deletes the media.",
     *     tags={"Race Event"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the race event",
     *         in="path",
     *         name="id",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Parameter(
     *         description="ID of the media",
     *         in="path",
     *         name="mediaId",
     *         required=true,
     *         type="string"
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Successful operation",
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="RaceEvent not found. Media not found"
     *     ),
     * )
     *
     * @Delete("/raceevents/{id}/media/{mediaId}")
     *
     * @param int $id
     * @param int $mediaId
     *
     * @return APIResponse
     */
    public function deleteAction($id, $mediaId)
    {
        $mediaService = $this->get('app.media.raceevents');
        $raceEventRepository = $this->get('app.repository.race_event');
        $apiResponseBuilder = $this->get('app.response.builder');
        $raceEvent = $raceEventRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($raceEvent === null) {
            return $apiResponseBuilder->buildNotFoundResponse('RaceEvent not found');
        }

        return $mediaService->deleteMedia($mediaId);
    }
}
