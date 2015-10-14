<?php

namespace AppBundle\Services;

use AppBundle\Response\APIResponse;
use AppBundle\Repository\JourneyRepository;
use AppBundle\Repository\UserRepository;
use AppBundle\Response\APIResponseBuilder;

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
     * @param JourneyRepository $journeyRepository
     * @param UserRepository $userRepository
     * @param APIResponseBuilder $apiResponseBuilder
     */
    public function __construct(JourneyRepository $journeyRepository, UserRepository $userRepository, APIResponseBuilder $apiResponseBuilder)
    {
        $this->journeyRepository = $journeyRepository;
        $this->userRepository = $userRepository;
        $this->apiResponseBuilder = $apiResponseBuilder;
    }
    
    /**
     * @param string $id 
     * @return APIResponse
     */
    public function buildGetAPIResponse($id) 
    {
        $journeys = $this->journeyRepository->findBy([
            'id' => $id,
            'publish' => true,
        ]);
        
        if (count($journeys) === 0) {
            return $this->apiResponseBuilder->buildNotFoundResponse('Journey not found.');
        }
        
        return $this->apiResponseBuilder->buildSuccessResponse($journeys, 'journeys');
    }
    
    /**
     * @param string $userId 
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
}
