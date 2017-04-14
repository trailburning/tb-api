<?php 

namespace AppBundle\Model;

use JMS\Serializer\Annotation as Serializer;

/**
* Description
*/
class Summary
{
    
    /**
     * @var int
     */
    private $countryCount;
    
    /**
     * @var int
     */
    private $raceEventCount;

    /**
     * @var int
     */
    private $userCount;
    
    /**
     * Get the value of raceEventCount.
     *
     * @return int
     */
    public function getRaceEventCount()
    {
        return $this->raceEventCount;
    }

    /**
     * Set the value of race event count.
     *
     * @param int $raceEventCount
     *
     * @return self
     */
    public function setRaceEventCount($raceEventCount)
    {
        $this->raceEventCount = $raceEventCount;

        return $this;
    }
    
    /**
     * Get the value of countryCount.
     *
     * @return int
     */
    public function getCountryCount()
    {
        return $this->countryCount;
    }

    /**
     * Set the value of country count.
     *
     * @param int $countryCount
     *
     * @return self
     */
    public function setCountryCount($countryCount)
    {
        $this->countryCount = $countryCount;

        return $this;
    }

    /**
     * Get the value of userCount.
     *
     * @return int
     */
    public function getUserCount()
    {
        return $this->userCount;
    }

    /**
     * Set the value of usercount.
     *
     * @param int $userCount
     *
     * @return self
     */
    public function setUserCount($userCount)
    {
        $this->userCount = $userCount;

        return $this;
    }

}