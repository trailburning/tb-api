<?php

namespace AppBundle\Handler;

use AppBundle\Entity\Asset;
use AppBundle\Form\Type\MediaUpdateType;
use AppBundle\Form\Type\MediaUploadType;
use AppBundle\Repository\AssetRepository;
use AppBundle\Services\MediaService;
use Exception;
use AppBundle\Model\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Entity\Media;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\HttpFoundation\Request;

/**
 * Asset handler.
 */
class AssetMediaHandler
{
    /**
     * @var AssetRepository
     */
    private $assetRepository;

    /**
     * @var APIResponseBuilder
     */
    private $apiResponseBuilder;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var Router
     */
    private $router;

    /**
     * @var MediaService
     */
    private $mediaService;

    /**
     * @param AssetRepository      $assetRepository
     * @param APIResponseBuilder   $apiResponseBuilder
     * @param FormFactoryInterface $formFactory
     * @param Router               $router
     * @param MediaService         $mediaService
     */
    public function __construct(
        AssetRepository $assetRepository,
        APIResponseBuilder $apiResponseBuilder,
        FormFactoryInterface $formFactory,
        Router $router,
        MediaService $mediaService
    ) {
        $this->assetRepository = $assetRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->mediaService = $mediaService;
    }

    /**
     * @param Request $request
     * @param Asset   $asset
     * @param Media   $media
     *
     * @return APIResponse
     *
     * @throws Exception
     */
    public function handleCreateOrUpdate(Request $request, Asset $asset, Media $media = null)
    {
        $statusCode = 201;
        /** @var Form $form */
        $form = $this->formFactory->create(MediaUploadType::class);
        if ($media !== null) {
            $statusCode = 204;
            $form = $this->formFactory->create(MediaUpdateType::class);
        }

        $form->handleRequest($request);

        if (!$form->isValid()) {
            return $this->apiResponseBuilder->buildFormErrorResponse($form);
        }

        $media = $this->mediaService->createOrUpdateMedia($form, $media);
        $asset->addMedia($media);
        $this->assetRepository->add($asset);
        $this->assetRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse($statusCode);
    }
}
