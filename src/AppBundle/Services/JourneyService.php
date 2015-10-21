<?php

namespace AppBundle\Services;

use AppBundle\Response\APIResponse;
use AppBundle\Repository\JourneyRepository;
use AppBundle\Entity\Journey;
use AppBundle\Entity\Route;
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
     * @param JourneyRepository  $journeyRepository
     * @param UserRepository     $userRepository
     * @param APIResponseBuilder $apiResponseBuilder
     * @param GPXParser          $gpxParser
     */
    public function __construct(
        JourneyRepository $journeyRepository,
        UserRepository $userRepository,
        APIResponseBuilder $apiResponseBuilder,
        GPXParser $gpxParser)
    {
        $this->journeyRepository = $journeyRepository;
        $this->userRepository = $userRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->gpxParser = $gpxParser;
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

        $journeys = $this->journeyRepository->findBy([
            'user' => $user,
            'publish' => true,
        ]);

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
            $journey->clearRoutes();
            foreach ($segments[0] as $routePoint) {
                $journey->addRoute(new Route(new Point($routePoint['long'], $routePoint['lat'], 4326)));
            }
        }    
        
        $this->journeyRepository->add($journey);
        $this->journeyRepository->store();
        
        return $this->apiResponseBuilder->buildSuccessResponse([$journey], 'journeys');
    }
}
