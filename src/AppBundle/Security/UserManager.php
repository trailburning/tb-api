<?php

namespace AppBundle\Security;

use FOS\UserBundle\Doctrine\UserManager as BaseUserManager;

/**
* UserManager
*/
class UserManager extends BaseUserManager
{
    /**
     * {@inheritDoc}
     */
    public function findUserBy(array $criteria)
    {
        $criteria['client'] = 'race_base';
        return $this->repository->findOneBy($criteria);
    }
    
}