<?php

namespace AppBundle\Handler;

use AppBundle\Response\APIResponse;
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
     * @param RegionRepository    $regionRepository
     * @param RaceEventRepository $raceEventRepository
     */
    public function __construct(
        APIResponseBuilder $apiResponseBuilder,
        RegionRepository $regionRepository,
        RaceEventRepository $raceEventRepository
    ) {
        $this->apiResponseBuilder = $apiResponseBuilder;
        $this->regionRepository = $regionRepository;
        $this->raceEventRepository = $raceEventRepository;
    }

    /**
     * @return APIResponse
     */
    public function handleGet()
    {
        $summary = new Summary();
        $summary->setRaceEventCount($this->raceEventRepository->getCount());
        $summary->setCountryCount($this->regionRepository->getCountryCount());

        return $this->apiResponseBuilder->buildSuccessResponse($summary, 'summary');
    }
}
