<?php

namespace AppBundle\Services;

use AppBundle\Response\APIResponse;
use AppBundle\Repository\JourneyRepository;

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
     * @param JourneyRepository $journeyRepository
     */
    public function __construct(JourneyRepository $journeyRepository)
    {
        $this->journeyRepository = $journeyRepository;
    }
    
    /**
     * @param string $id 
     * @return APIResponse
     */
    public function buildGetAPIResponse($id) 
    {
        $response = new APIResponse();
        $journey = $this->journeyRepository->findBy([
            'id' => $id,
        ]);
        
        if (count($journey) === 0) {
            $response->setStatusCode(404);
            $response->setStatus('error');
            
            return $response;
        }
        
        $response->addToBody($journey, 'journeys');
        
        return $response;
    }
}
