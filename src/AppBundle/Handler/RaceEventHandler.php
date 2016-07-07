<?php

namespace AppBundle\Handler;

use Exception;
use AppBundle\Model\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Repository\RaceEventRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use AppBundle\Entity\RaceEvent;
use AppBundle\Services\SearchIndexService;
use AppBundle\Services\MapboxAPI;
use AppBundle\Repository\RegionRepository;

/**
 * RaceEvent handler.
 */
class RaceEventHandler
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
     * @var MapboxAPI
     */
    private $mapboxAPI;

    /**
     * @var RegionRepository
     */
    private $regionRepository;

    /**
     * @param RaceEventRepository  $raceEventRepository
     * @param APIResponseBuilder   $apiResponseBuilder
     * @param FormFactoryInterface $formFactory
     * @param Router               $router
     * @param SearchIndexService   $searchIndexService
     * @param MapboxAPI            $mapboxAPI
     * @param RegionRepository     $regionRepository
     */
    public function __construct(
        RaceEventRepository $raceEventRepository,
        APIResponseBuilder $apiResponseBuilder,
        FormFactoryInterface $formFactory,
        Router $router,
        SearchIndexService $searchIndexService,
        MapboxAPI $mapboxAPI,
        RegionRepository $regionRepository
    ) {
        $this->raceEventRepository = $raceEventRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->searchIndexService = $searchIndexService;
        $this->mapboxAPI = $mapboxAPI;
        $this->regionRepository = $regionRepository;
    }

    /**
     * @param string $id
     *
     * @return APIResponse
     */
    public function handleGet($id)
    {
        $raceEvents = $this->raceEventRepository->findBy([
            'oid' => $id,
        ]);

        if (count($raceEvents) === 0) {
            return $this->apiResponseBuilder->buildNotFoundResponse('RaceEvent not found.');
        }

        return $this->apiResponseBuilder->buildSuccessResponse($raceEvents, 'raceevents');
    }

    /**
     * @return APIResponse
     */
    public function handleGetList()
    {
        $raceEvents = $this->raceEventRepository->findAll();

        return $this->apiResponseBuilder->buildSuccessResponse($raceEvents, 'raceevents');
    }

    /**
     * @param array     $parameters
     * @param RaceEvent $raceEvent
     * @param string    $method
     *
     * @return APIResponse
     */
    public function handleCreateOrUpdate(array $parameters, RaceEvent $raceEvent = null, $method = 'POST')
    {
        if ($raceEvent === null) {
            $raceEvent = new RaceEvent();
        }

        $form = $this->formFactory->create('AppBundle\Form\Type\RaceEventType', $raceEvent, ['method' => $method]);
        $clearMissing = ($method !== 'PUT') ? true : false;
        $form->submit($parameters, $clearMissing);

        if (!$form->isValid()) {
            return $this->apiResponseBuilder->buildFormErrorResponse($form);
        }

        $raceEvent = $form->getData();
        $this->setRegionsFromCoords($raceEvent, $parameters);

        $this->raceEventRepository->beginnTransaction();
        try {
            $this->raceEventRepository->add($raceEvent);
            $this->raceEventRepository->store();
            if ($method === 'POST') {
                $this->searchIndexService->createRaceEvent($raceEvent);
            } else {
                $this->searchIndexService->updateRaceEvent($raceEvent);
            }
            $this->raceEventRepository->commit();
        } catch (Exception $e) {
            $this->raceEventRepository->rollback();
            throw $e;
        }

        $response = $this->apiResponseBuilder->buildEmptyResponse(204);
        if ($method === 'POST') {
            $response->setStatusCode(201);
            $location = $this->router->generate('get_race_event', ['id' => $raceEvent->getOid()]);
            $response->addHeader('Location', $location);
        }

        return $response;
    }

    /**
     * @param RaceEvent $raceEvent
     * @param array     $parameters
     */
    private function setRegionsFromCoords(RaceEvent $raceEvent, array $parameters)
    {
        if (!isset($parameters['coords']) || $raceEvent->getCoords() === null) {
            return;
        }

        $regionFeatures = $this->mapboxAPI->reverseGeocode($raceEvent->getCoords());
        if (!isset($parameters['location'])) {
            $raceEvent->setLocation($this->mapboxAPI->getLocationNameFromFeatures($regionFeatures));
        }

        $regions = [];
        foreach ($regionFeatures as $regionFeature) {
            $bboxRadius = $this->mapboxAPI->calculateBoundingBoxRadius(
                $regionFeature->bbox[0], 
                $regionFeature->bbox[1], 
                $regionFeature->bbox[2], 
                $regionFeature->bbox[3]
            );
            $region = $this->regionRepository->getOrCreateRegion(
                $regionFeature->id, 
                $regionFeature->place_name, 
                $regionFeature->center[0], 
                $regionFeature->center[1], 
                $bboxRadius
            );
            $regions[] = $region;
        }
        
        $raceEvent->setRegions($regions);
    }

    /**
     * @param string $id
     *
     * @return APIResponse
     */
    public function handleDelete($id)
    {
        $raceEvent = $this->raceEventRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($raceEvent === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('RaceEvent not found');
        }

        $this->raceEventRepository->beginnTransaction();
        try {
            $this->raceEventRepository->remove($raceEvent);
            $this->raceEventRepository->store();
            $this->searchIndexService->deleteRaceEvent($raceEvent);
            $this->raceEventRepository->commit();
        } catch (Exception $e) {
            $this->raceEventRepository->rollback();
            throw $e;
        }

        return $this->apiResponseBuilder->buildEmptyResponse(204);
    }
}
