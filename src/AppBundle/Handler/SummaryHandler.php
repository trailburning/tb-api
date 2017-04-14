<?php

namespace AppBundle\Handler;

use AppBundle\Model\APIResponse;
use AppBundle\Repository\UserRepository;
use AppBundle\Services\APIResponseBuilder;
use AppBundle\Model\Summary;
use AppBundle\Repository\RaceEventRepository;
use AppBundle\Repository\RegionRepository;

/**
 * Race handler.
 */
class SummaryHandler
{
    /**
     * @var APIResponseBuilder
     */
    private $apiResponseBuilder;

    /**
     * @var RegionRepository
     */
    private $regionRepository;

    /**
     * @var RaceEventRepository
     */
    private $raceEventRepository;

    /**
     * @var UserRepository
     */
    private $userRepository;


    public function __construct(
        APIResponseBuilder $apiResponseBuilder,
        RegionRepository $regionRepository,
        RaceEventRepository $raceEventRepository,
        UserRepository $userRepository
    ) {
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->regionRepository = $regionRepository;
        $this->raceEventRepository = $raceEventRepository;
        $this->userRepository = $userRepository;
    }

    /**
     * @return APIResponse
     */
    public function handleGet()
    {
        $summary = new Summary();
        $summary->setRaceEventCount($this->raceEventRepository->getCount());
        $summary->setCountryCount($this->regionRepository->getCountryCount());
        $summary->setUserCount($this->userRepository->getUserCount());

        return $this->apiResponseBuilder->buildSuccessResponse($summary, 'summary');
    }
}
