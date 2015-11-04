<?php

namespace AppBundle\Services;

use AppBundle\Response\APIResponse;
use AppBundle\Repository\AssetRepository;
use AppBundle\Repository\AssetCategoryRepository;
use AppBundle\Repository\EventRepository;
use AppBundle\Response\APIResponseBuilder;
use AppBundle\Entity\Asset;
use AppBundle\Form\Type\AssetType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class AssetService.
 */
class AssetService
{
    /**
     * @var AssetRepository
     */
    protected $assetRepository;

    /**
     * @var EventRepository
     */
    protected $eventRepository;

    /**
     * @var APIResponseBuilder
     */
    protected $apiResponseBuilder;

    /**
     * @var AssetCategoryRepository
     */
    protected $assetCategoryRepository;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param AssetRepository         $assetRepository
     * @param EventRepository         $eventRepository
     * @param APIResponseBuilder      $apiResponseBuilder
     * @param AssetCategoryRepository $assetCategoryRepository
     * @param FormFactoryInterface    $formFactory
     * @param Router                  $router
     */
    public function __construct(
        AssetRepository $assetRepository,
        EventRepository $eventRepository,
        APIResponseBuilder $apiResponseBuilder,
        AssetCategoryRepository $assetCategoryRepository,
        FormFactoryInterface $formFactory,
        Router $router
    ) {
        $this->assetRepository = $assetRepository;
        $this->eventRepository = $eventRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->assetCategoryRepository = $assetCategoryRepository;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @param int $eventOid
     *
     * @return APIResponse
     */
    public function buildGetByEventAPIResponse($eventOid)
    {
        $event = $this->eventRepository->findOneBy([
            'oid' => $eventOid,
        ]);

        if ($event === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Event not found.');
        }

        $qb = $this->assetRepository->findByEventQB($event);
        $qb = $this->assetRepository->addOrderByPositionQB($qb);
        $assets = $qb->getQuery()->getResult();

        return $this->apiResponseBuilder->buildSuccessResponse($assets, 'assets');
    }

    /**
     * @param string $oid
     *
     * @return APIResponse
     */
    public function buildGetAPIResponse($oid)
    {
        $assets = $this->assetRepository->findBy([
            'oid' => $oid,
        ]);

        if (count($assets) === 0) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Assets not found.');
        }

        return $this->apiResponseBuilder->buildSuccessResponse($assets, 'assets');
    }

    /**
     * @param array  $parameters
     * @param Asset  $asset
     * @param string $method
     *
     * @return APIResponse
     */
    public function createOrUpdateFromAPI(array $parameters, Asset $asset = null, $method = 'POST')
    {
        if ($asset === null) {
            $asset = new Asset();
        }

        $form = $this->formFactory->create(new AssetType($this->assetCategoryRepository), $asset, ['method' => $method]);
        $clearMissing = ($method !== 'PUT') ? true : false;
        $form->submit($parameters, $clearMissing);

        if (!$form->isValid()) {
            return $this->apiResponseBuilder->buildFormErrorResponse($form);
        }

        $asset = $form->getData();

        $this->assetRepository->add($asset);
        $this->assetRepository->store();

        $response = $this->apiResponseBuilder->buildEmptyResponse(204);
        if ($method === 'POST') {
            $response->setStatusCode(201);
            $location = $this->router->generate('get_assets', ['id' => $asset->getOid()]);
            $response->addHeader('Location', $location);
        }

        return $response;
    }

    /**
     * @param string $id
     *
     * @return APIResponse
     */
    public function deleteFromAPI($id)
    {
        $asset = $this->assetRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($asset === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Asset not found');
        }

        $this->assetRepository->remove($asset);
        $this->assetRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse(204);
    }
}
