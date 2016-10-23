<?php

namespace AppBundle\Handler;

use Exception;
use AppBundle\Model\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Repository\RaceEventRepository;
use AppBundle\Repository\RaceEventAttributeRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use AppBundle\Entity\RaceEvent;
use AppBundle\Entity\RaceEventAttribute;
use AppBundle\Services\SearchIndexService;
use AppBundle\Services\MapboxAPI;
use AppBundle\Repository\RegionRepository;

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
     * @param RaceEventRepository  $raceEventRepository
     * @param RaceEventAttributeRepository  $raceEventAttributeRepository
     * @param APIResponseBuilder   $apiResponseBuilder
     * @param FormFactoryInterface $formFactory
     * @param Router               $router
     */
    public function __construct(
        RaceEventRepository $raceEventRepository,
        RaceEventAttributeRepository $raceEventAttributeRepository,
        APIResponseBuilder $apiResponseBuilder,
        FormFactoryInterface $formFactory,
        Router $router
    ) {
        $this->raceEventRepository = $raceEventRepository;
        $this->raceEventAttributeRepository = $raceEventAttributeRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @param string $raceEventId
     * @return APIResponse
     */
    public function handleGetList($raceEventId)
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
        $this->raceEventRepository->add($raceEvent);
        $this->raceEventRepository->store();

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
        $this->raceEventRepository->add($raceEvent);
        $this->raceEventRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse(204);
    }
}
