<?php

namespace AppBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Swagger\Annotations as SWG;
use FOS\RestBundle\Routing\ClassResourceInterface;
use FOS\RestBundle\Controller\Annotations\Get;
use FOS\RestBundle\Controller\Annotations\Post;
use AppBundle\Form\Type\MediaUploadType;

class MediaController extends Controller implements ClassResourceInterface
{

    /**
     * @SWG\Post(
     *     path="/assets/{id}/media",
     *     summary="Add a medias to an asset",
     *     description="Adds one or more media file to an asset.",
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
     *         name="file",
     *         required=true,
     *         type="file"
     *     ),
     *     @SWG\Response(
     *         response=201,
     *         description="Successful operation",
     *         @SWG\Schema(ref="#/definitions/Media")
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
    public function postAction($id)
    {
        $mediaService = $this->get('tb.media');
        $assetRepository = $this->get('tb.asset.repository');
        $apiResponseBuilder = $this->get('tb.response.builder');
        
        $asset = $assetRepository->findOneBy([
            'oid' => $id,
        ]);
        
        if ($asset === null) {
            return $apiResponseBuilder->buildNotFoundResponse('Asset not found');
        }
            
        $form = $this->createForm(new MediaUploadType());
        $form->handleRequest($this->getRequest());

        if (!$form->isValid()) {
            return $apiResponseBuilder->buildBadRequestResponse((string)$form->getErrors(true, true));
        }
        
        $mediaFiles = $form->get('media')->getData();
        // FIXME: multi media upload not working because of File validator
        if (!is_array($mediaFiles)) {
            $mediaFiles = [$mediaFiles];
        }
        
        return $mediaService->uploadMedia($mediaFiles, $asset);
    }
}
