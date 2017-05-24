<?php

namespace AppBundle\Handler;

use AppBundle\Model\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Repository\RaceEventRepository;
use AppBundle\Repository\RaceEventAttributeRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use AppBundle\Entity\RaceEvent;
use AppBundle\Entity\RaceEventAttribute;
use AppBundle\Services\SearchIndexService;

/**
 * RaceEvent handler.
 */
class RaceEventAttributeHandler
{
    /**
     * @var RaceEventRepository
     */
    private $raceEventRepository;

    /**
     * @var RaceEventAttributeRepository
     */
    private $raceEventAttributeRepository;

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
     * @param RaceEventRepository          $raceEventRepository
     * @param RaceEventAttributeRepository $raceEventAttributeRepository
     * @param APIResponseBuilder           $apiResponseBuilder
     * @param FormFactoryInterface         $formFactory
     * @param Router                       $router
     * @param SearchIndexService           $searchIndexService
     */
    public function __construct(
        RaceEventRepository $raceEventRepository,
        RaceEventAttributeRepository $raceEventAttributeRepository,
        APIResponseBuilder $apiResponseBuilder,
        FormFactoryInterface $formFactory,
        Router $router,
        SearchIndexService $searchIndexService
    ) {
        $this->raceEventRepository = $raceEventRepository;
        $this->raceEventAttributeRepository = $raceEventAttributeRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->searchIndexService = $searchIndexService;
    }

    /**
     * @return APIResponse
     */
    public function handleGetList()
    {
        $attributes = $this->raceEventAttributeRepository->findAll();

        return $this->apiResponseBuilder->buildSuccessResponse($attributes, 'raceeventattributes');
    }

    /**
     * @param string $raceEventId
     *
     * @return APIResponse
     */
    public function handleGetRaceEventList($raceEventId)
    {
        $raceEvent = $this->raceEventRepository->findOneBy([
            'oid' => $raceEventId,
        ]);
        if ($raceEvent === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('RaceEvent not found');
        }

        $attributes = $this->raceEventAttributeRepository->findByRaceEvent($raceEvent);

        return $this->apiResponseBuilder->buildSuccessResponse($attributes, 'raceeventattributes');
    }

    /**
     * @param string $raceEventId
     * @param string $raceEventAttributeId
     *
     * @return APIResponse
     */
    public function handleAdd($raceEventId, $raceEventAttributeId)
    {
        $raceEvent = $this->raceEventRepository->findOneBy([
            'oid' => $raceEventId,
        ]);
        if ($raceEvent === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('RaceEvent not found');
        }

        $raceEventAttribute = $this->raceEventAttributeRepository->findOneBy([
            'id' => $raceEventAttributeId,
        ]);
        if ($raceEventAttribute === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('RaceEventAttribute not found');
        }

        $raceEvent->addAttribute($raceEventAttribute);
        
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

        return $this->apiResponseBuilder->buildEmptyResponse(204);
    }

    /**
     * @param string $id
     *
     * @return APIResponse
     */
    public function handleRemove($raceEventId, $raceEventAttributeId)
    {
        $raceEvent = $this->raceEventRepository->findOneBy([
            'oid' => $raceEventId,
        ]);
        if ($raceEvent === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('RaceEvent not found');
        }

        $raceEventAttribute = $this->raceEventAttributeRepository->findOneBy([
            'id' => $raceEventAttributeId,
        ]);
        if ($raceEventAttribute === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('RaceEventAttribute not found');
        }

        $raceEvent->removeAttribute($raceEventAttribute);
        
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

        return $this->apiResponseBuilder->buildEmptyResponse(204);
    }
}
