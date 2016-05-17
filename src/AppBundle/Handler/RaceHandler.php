<?php

namespace AppBundle\Handler;

use AppBundle\Response\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Repository\RaceRepository;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use AppBundle\Entity\Race;
use AppBundle\Entity\RaceEvent;
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
     * @param RaceRepository $raceRepository
     * @param APIResponseBuilder $apiResponseBuilder
     * @param FormFactoryInterface $formFactory
     * @param Router $router 
     */
    public function __construct(
        RaceRepository $raceRepository,
        APIResponseBuilder $apiResponseBuilder,
        FormFactoryInterface $formFactory,
        Router $router 
    ) {
        $this->raceRepository = $raceRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
                $this->formFactory = $formFactory;
        $this->router = $router; 
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
     * @param array   $parameters
     * @param Race $race
     * @param string  $method
     *
     * @return APIResponse
     */
    public function handleCreateOrUpdate(array $parameters, Race $race = null, $method = 'POST')
    {
        if ($race === null) {
            $race = new Race();
        }
        
        $form = $this->formFactory->create('AppBundle\Form\RaceType', $race, ['method' => $method]);
        $clearMissing = ($method !== 'PUT') ? true : false;
        $form->submit($parameters, $clearMissing);

        if (!$form->isValid()) {
            return $this->apiResponseBuilder->buildFormErrorResponse($form);
        }

        $race = $form->getData();

        $this->raceRepository->add($race);
        $this->raceRepository->store();

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

        $this->raceRepository->remove($race);
        $this->raceRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse(204);
    }
}
