<?php

namespace AppBundle\Services;

use AppBundle\Entity\Journey;
use AppBundle\Entity\RoutePoint;
use AppBundle\Form\Type\JourneyType;
use AppBundle\Repository\JourneyRepository;
use AppBundle\Repository\RoutePointRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Model\APIResponse;
use AppBundle\Services\APIResponseBuilder;
use CrEOF\Spatial\PHP\Types\Geometry\Point;
use Symfony\Bundle\FrameworkBundle\Routing\Router;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

/**
 * Class JourneyService.
 */
class JourneyService
{
    /**
     * @var JourneyRepository
     */
    private $journeyRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;

    /**
     * @var APIResponseBuilder
     */
    private $apiResponseBuilder;

    /**
     * @var GPXParser
     */
    private $gpxParser;

    /**
     * @var RoutePointRepository
     */
    private $routePointRepository;

    /**
     * @var FormFactoryInterface
     */
    private $formFactory;

    /**
     * @var Router
     */
    private $router;

    /**
     * @param JourneyRepository    $journeyRepository
     * @param UserRepository       $userRepository
     * @param APIResponseBuilder   $apiResponseBuilder
     * @param GPXParser            $gpxParser
     * @param RoutePointRepository $routePointRepository
     * @param FormFactoryInterface $formFactory
     * @param Router               $router
     */
    public function __construct(
        JourneyRepository $journeyRepository,
        UserRepository $userRepository,
        APIResponseBuilder $apiResponseBuilder,
        GPXParser $gpxParser,
        RoutePointRepository $routePointRepository,
        FormFactoryInterface $formFactory,
        Router $router
    ) {
        $this->journeyRepository = $journeyRepository;
        $this->userRepository = $userRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->gpxParser = $gpxParser;
        $this->routePointRepository = $routePointRepository;
        $this->formFactory = $formFactory;
        $this->router = $router;
    }

    /**
     * @param string $oid
     *
     * @return APIResponse
     */
    public function buildGetAPIResponse($oid)
    {
        $journeys = $this->journeyRepository->findBy([
            'oid' => $oid,
            'publish' => true,
        ]);

        if (count($journeys) === 0) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Journey not found.');
        }

        return $this->apiResponseBuilder->buildSuccessResponse($journeys, 'journeys');
    }

    /**
     * @param string $userId
     *
     * @return APIResponse
     */
    public function buildGetByUserAPIResponse($userId)
    {
        $user = $this->userRepository->findOneBy([
            'id' => $userId,
        ]);

        if ($user === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('User not found.');
        }

        $qb = $this->journeyRepository->findPublishedByUserQB($user);
        $qb = $this->journeyRepository->addOrderByPositionQB($qb);
        $journeys = $qb->getQuery()->getResult();

        // FIXME: removes routes from list response via response groups
        foreach ($journeys as $journey) {
            $journey->setNullRoutePoints();
        }

        return $this->apiResponseBuilder->buildSuccessResponse($journeys, 'journeys');
    }

    /**
     * @param UploadedFile $file
     * @param Journey      $journey
     *
     * @return APIResponse
     */
    public function importGPX(UploadedFile $file, Journey $journey)
    {
        $gpx = file_get_contents($file->getPathname());
        $segments = $this->gpxParser->parse($gpx);

        if (isset($segments[0])) {
            $this->routePointRepository->deleteByJourney($journey);
            $journey->clearRoutePoints();
            foreach ($segments[0] as $routePoint) {
                $elevation = isset($routePoint['elevation']) ? $routePoint['elevation'] : null;
                $point = new Point($routePoint['long'], $routePoint['lat'], 4326);
                $journey->addRoutePoint(new RoutePoint($point, $elevation));
            }
        }

        $this->journeyRepository->add($journey);
        $this->journeyRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse(201);
    }

    /**
     * @param Journey $journey
     *
     * @return APIResponse
     */
    public function deleteJourneyRoutePoints($oid)
    {
        $journey = $this->journeyRepository->findOneBy([
            'oid' => $oid,
        ]);

        if ($journey === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Journey not found.');
        }

        $this->routePointRepository->deleteByJourney($journey);
        $journey->clearRoutePoints();

        return $this->apiResponseBuilder->buildSuccessResponse([$journey], 'journeys');
    }

    /**
     * @param Journey $journey
     * @param array   $parameters
     * @param string  $method
     *
     * @return APIResponse
     */
    public function createOrUpdateFromAPI(array $parameters, Journey $journey = null, $method = 'POST')
    {
        if ($journey === null) {
            $journey = new Journey();
        }

        $form = $this->formFactory->create(new JourneyType(), $journey, ['method' => $method]);
        $clearMissing = ($method !== 'PUT') ? true : false;
        $form->submit($parameters, $clearMissing);

        if (!$form->isValid()) {
            return $this->apiResponseBuilder->buildFormErrorResponse($form);
        }

        $journey = $form->getData();

        $this->journeyRepository->add($journey);
        $this->journeyRepository->store();

        $response = $this->apiResponseBuilder->buildEmptyResponse(204);
        if ($method === 'POST') {
            $response->setStatusCode(201);
            $location = $this->router->generate('get_journeys', ['id' => $journey->getOid()]);
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
        $journey = $this->journeyRepository->findOneBy([
            'oid' => $id,
        ]);
        if ($journey === null) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Journey not found');
        }

        $this->journeyRepository->remove($journey);
        $this->journeyRepository->store();

        return $this->apiResponseBuilder->buildEmptyResponse(204);
    }
}
