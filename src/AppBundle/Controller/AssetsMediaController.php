<?php

namespace AppBundle\Controller;

use AppBundle\Form\Type\MediaUploadType;
use FOS\RestBundle\Controller\Annotations\Delete;
use FOS\RestBundle\Controller\Annotations\Post;
use FOS\RestBundle\Routing\ClassResourceInterface;
use Swagger\Annotations as SWG;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class AssetsMediaController extends Controller implements ClassResourceInterface
{

    /**
     * @SWG\Post(
     *     path="/assets/{id}/media",
     *     summary="Add a medias to an asset",
     *     description="Adds a more media file to an asset.",
     *     tags={"Assets"},
     *     consumes={"multipart/form-data"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the asset",
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
     *     @SWG\Response(
     *         response=201,
     *         description="Successful operation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Asset not found"
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid MIME type. File size too large."
     *     ),
     * )
     *
     * @Post("/assets/{id}/media")
     *
     * @param int $id
     *
     * @return APIResponse
     */
    public function postAction(Request $request, $id)
    {
        $mediaService = $this->get('app.media');
        $assetRepository = $this->get('app.asset.repository');
        $apiResponseBuilder = $this->get('app.response.builder');

        $asset = $assetRepository->findOneBy([
            'oid' => $id,
        ]);

        if ($asset === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Asset not found');
        }

        $form = $this->createForm(MediaUploadType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $apiResponseBuilder->buildBadRequestResponse((string)$form->getErrors(true, true));
        }

        $mediaFiles = $form->get('media')->getData();
        // FIXME: multi media upload not working because of File validator
        if (!is_array($mediaFiles)) {
            $mediaFiles = [$mediaFiles];
        }

        return $mediaService->createMedia($mediaFiles, $asset);
    }

    /**
     * @SWG\Post(
     *     path="/assets/{id}/media/{mediaId}",
     *     summary="Updates the media of an asset",
     *     description="Updates the media of an asset.",
     *     tags={"Assets"},
     *     consumes={"multipart/form-data"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the asset",
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
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Response(
     *         response=204,
     *         description="Successful operation"
     *     ),
     *     @SWG\Response(
     *         response="404",
     *         description="Asset not found. Media not found."
     *     ),
     *     @SWG\Response(
     *         response="400",
     *         description="Invalid MIME type. File size too large."
     *     ),
     * )
     *
     * @Post("/assets/{id}/media/{mediaId}")
     *
     * @param int $id
     * @param int $mediaId
     *
     * @return APIResponse
     */
    public function putAction(Request $request, $id, $mediaId)
    {
        $mediaService = $this->get('app.media');
        $assetRepository = $this->get('app.asset.repository');
        $mediaRepository = $this->get('app.media.repository');
        $apiResponseBuilder = $this->get('app.response.builder');

        $asset = $assetRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($asset === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Asset not found');
        }

        $media = $mediaRepository->findOneBy([
            'oid' => $mediaId,
        ]);
        if ($media === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Media not found');
        }

        $form = $this->createForm(MediaUploadType::class);
        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $apiResponseBuilder->buildBadRequestResponse((string)$form->getErrors(true, true));
        }

        $file = $form->get('media')->getData();

        return $mediaService->updateMedia($file, $media);
    }

    /**
     * @SWG\Delete(
     *     path="/assets/{id}/media/{mediaId}",
     *     summary="Delete a media",
     *     description="Deletes the media.",
     *     tags={"Assets"},
     *     produces={"application/json"},
     *     @SWG\Parameter(
     *         description="ID of the asset",
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
     *         description="Asset not found. Media not found"
     *     ),
     * )
     *
     * @Delete("/assets/{id}/media/{mediaId}")
     *
     * @param int $id
     * @param int $mediaId
     *
     * @return APIResponse
     */
    public function deleteAction($id, $mediaId)
    {
        $mediaService = $this->get('app.media');
        $assetRepository = $this->get('app.asset.repository');
        $apiResponseBuilder = $this->get('app.response.builder');
        $asset = $assetRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($asset === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Asset not found');
        }

        return $mediaService->deleteMedia($mediaId);
    }
}
