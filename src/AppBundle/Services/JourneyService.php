<?php

namespace AppBundle\Services;

use AppBundle\Response\APIResponse;
use AppBundle\Repository\JourneyRepository;
use AppBundle\Repository\RoutePointRepository;
use AppBundle\Entity\Journey;
use AppBundle\Entity\RoutePoint;
use AppBundle\Repository\UserRepository;
use AppBundle\Response\APIResponseBuilder;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use CrEOF\Spatial\PHP\Types\Geometry\Point;

/**
 * Class JourneyService.
 */
class JourneyService
{
    /**
     * @var JourneyRepository
     */
    protected $journeyRepository;

    /**
     * @var UserRepository
     */
    protected $userRepository;

    /**
     * @var APIResponseBuilder
     */
    protected $apiResponseBuilder;

    /**
     * @var GPXParser
     */
    protected $gpxParser;

    /**
     * @var RoutePointRepository
     */
    protected $routePointRepository;

    /**
     * @param JourneyRepository    $journeyRepository
     * @param UserRepository       $userRepository
     * @param APIResponseBuilder   $apiResponseBuilder
     * @param GPXParser            $gpxParser
     * @param RoutePointRepository $routePointRepository
     */
    public function __construct(
        JourneyRepository $journeyRepository,
        UserRepository $userRepository,
        APIResponseBuilder $apiResponseBuilder,
        GPXParser $gpxParser,
        RoutePointRepository $routePointRepository)
    {
        $this->journeyRepository = $journeyRepository;
        $this->userRepository = $userRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->gpxParser = $gpxParser;
        $this->routePointRepository = $routePointRepository;
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
                $journey->addRoutePoint(new RoutePoint(new Point($routePoint['long'], $routePoint['lat'], 4326)));
            }
        }

        $this->journeyRepository->add($journey);
        $this->journeyRepository->store();

        return $this->apiResponseBuilder->buildSuccessResponse([$journey], 'journeys');
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
}
