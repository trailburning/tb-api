<?php

namespace AppBundle\Handler;

use Exception;
use AppBundle\Model\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Repository\RaceRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use AppBundle\Entity\Race;
use AppBundle\DBAL\Types\RaceCategory;
use AppBundle\Entity\RaceEvent;
use AppBundle\Services\SearchIndexService;
use AppBundle\Repository\RaceEventRepository;

/**
 * Race handler.
 */
class RaceHandler
{
    /**
     * @var RaceRepository
     */
    private $raceRepository;

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
     * @var RaceEventRepository
     */
    private $raceEventRepository;

    /**
     * @param RaceRepository       $raceRepository
     * @param APIResponseBuilder   $apiResponseBuilder
     * @param FormFactoryInterface $formFactory
     * @param Router               $router
     * @param SearchIndexService   $searchIndexService
     * @param RaceEventRepository  $raceEventRepository
     */
    public function __construct(
        RaceRepository $raceRepository,
        APIResponseBuilder $apiResponseBuilder,
        FormFactoryInterface $formFactory,
        Router $router,
        SearchIndexService $searchIndexService,
        RaceEventRepository $raceEventRepository
    ) {
        $this->raceRepository = $raceRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->raceEventRepository = $raceEventRepository;
        $this->searchIndexService = $searchIndexService;
    }

    /**
     * @param string $id
     *
     * @return APIResponse
     */
    public function handleGet($id)
    {
        $races = $this->raceRepository->findBy([
            'oid' => $id,
        ]);

        if (count($races) === 0) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Race not found.');
        }

        return $this->apiResponseBuilder->buildSuccessResponse($races, 'races');
    }

    /**
     * @return APIResponse
     */
    public function handleGetList()
    {
        $races = $this->raceRepository->findAll();

        return $this->apiResponseBuilder->buildSuccessResponse($races, 'races');
    }

    /**
     * @param RaceEvent $raceEvent
     *
     * @return APIResponse
     */
    public function handleGetListFilteredByRaceEvent(RaceEvent $raceEvent)
    {
        $races = $this->raceRepository->findBy([
            'raceEvent' => $raceEvent,
        ]);

        return $this->apiResponseBuilder->buildSuccessResponse($races, 'races');
    }

    /**
     * @param array  $parameters
     * @param Race   $race
     * @param string $method
     *
     * @return APIResponse
     */
    public function handleCreateOrUpdate(array $parameters, Race $race = null, $method = 'POST')
    {
        if ($race === null) {
            $race = new Race();
        }
        
        $parameters = $this->setCategoryFromDistance($parameters);
        
        $form = $this->formFactory->create('AppBundle\Form\Type\RaceType', $race, ['method' => $method]);
        $clearMissing = ($method !== 'PUT') ? true : false;
        $form->submit($parameters, $clearMissing);

        if (!$form->isValid()) {
            return $this->apiResponseBuilder->buildFormErrorResponse($form);
        }

        $race = $form->getData();
        $raceEvent = $this->raceEventRepository->findOneBy([
            'id' => $race->getRaceEvent()->getId(),
        ]);
        
        $this->raceRepository->beginnTransaction();
        try {
            $this->raceRepository->add($race);
            $this->raceRepository->store();
            $this->searchIndexService->updateRaceEvent($raceEvent);
            $this->raceRepository->commit();
        } catch (Exception $e) {
            $this->raceRepository->rollback();
            throw $e;
        }

        $response = $this->apiResponseBuilder->buildEmptyResponse(204);
        if ($method === 'POST') {
            $response->setStatusCode(201);
            $location = $this->router->generate('get_race', ['id' => $race->getOid()]);
            $response->addHeader('Location', $location);
        }

        return $response;
    }

    /**
     * @param string $id
     *
     * @return APIResponse
     */
    public function handleDelete($id)
    {
        $race = $this->raceRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($race === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Race not found');
        }

        $raceEvent = $this->raceEventRepository->findOneBy([
            'id' => $race->getRaceEvent()->getId(),
        ]);

        $this->raceRepository->beginnTransaction();
        try {
            $this->raceRepository->remove($race);
            $this->raceRepository->store();
            $this->searchIndexService->updateRaceEvent($raceEvent);
            $this->raceRepository->commit();
        } catch (Exception $e) {
            $this->raceRepository->rollback();
            throw $e;
        }

        return $this->apiResponseBuilder->buildEmptyResponse(204);
    }
    
    /**
     * @param array $parameters 
     * @return array
     */
    private function setCategoryFromDistance(array $parameters): array
    {
        if (!isset($parameters['distance']) || isset($parameters['category'])) {
            return $parameters;
        }
        
        if ($parameters['distance'] >= 45000) {
            $parameters['category'] = RaceCategory::ULTRA_MARATHON;
        } elseif ($parameters['distance'] >= 40000) {
            $parameters['category'] = RaceCategory::MARATHON;
        } elseif ($parameters['distance'] >= 20000) {
            $parameters['category'] = RaceCategory::HALF_MARATHON;
        } elseif ($parameters['distance'] >= 10000) {
            $parameters['category'] = RaceCategory::TEN_K;
        } elseif ($parameters['distance'] >= 5000) {
            $parameters['category'] = RaceCategory::FIVE_K;
        }
        
        return $parameters;
    }
}
