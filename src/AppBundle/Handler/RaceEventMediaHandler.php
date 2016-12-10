<?php

namespace AppBundle\Handler;

use AppBundle\Form\Type\MediaUpdateType;
use AppBundle\Form\Type\MediaUploadType;
use AppBundle\Services\MediaService;
use Exception;
use AppBundle\Model\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Repository\RaceEventRepository;
use AppBundle\Entity\Media;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use AppBundle\Entity\RaceEvent;
use AppBundle\Services\SearchIndexService;
use Symfony\Component\HttpFoundation\Request;

/**
 * RaceEvent handler.
 */
class RaceEventMediaHandler
{
    /**
     * @var RaceEventRepository
     */
    private $raceEventRepository;

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
     * @var SearchIndexService
     */
    private $searchIndexService;

    /**
     * @var MediaService
     */
    private $mediaService;

    /**
     * @param RaceEventRepository  $raceEventRepository
     * @param APIResponseBuilder   $apiResponseBuilder
     * @param FormFactoryInterface $formFactory
     * @param Router               $router
     * @param SearchIndexService   $searchIndexService
     * @param MediaService         $mediaService
     */
    public function __construct(
        RaceEventRepository $raceEventRepository,
        APIResponseBuilder $apiResponseBuilder,
        FormFactoryInterface $formFactory,
        Router $router,
        SearchIndexService $searchIndexService,
        MediaService $mediaService
    ) {
        $this->raceEventRepository = $raceEventRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->searchIndexService = $searchIndexService;
        $this->mediaService = $mediaService;
    }

    /**
     * @param Request   $request
     * @param RaceEvent $raceEvent
     * @param Media     $media
     *
     * @return APIResponse
     *
     * @throws Exception
     */
    public function handleCreateOrUpdate(Request $request, RaceEvent $raceEvent, Media $media = null)
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

        $raceEvent->addMedia($media);

        $this->raceEventRepository->beginnTransaction();
        try {
            $this->raceEventRepository->add($raceEvent);
            $this->raceEventRepository->store();
            $this->searchIndexService->updateRaceEvent($raceEvent);
            $this->raceEventRepository->commit();
        } catch (Exception $e) {
            $this->raceEventRepository->rollback();
            throw $e;
        }

        return $this->apiResponseBuilder->buildEmptyResponse($statusCode);
    }
}
