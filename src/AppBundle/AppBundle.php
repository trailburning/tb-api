<?php

namespace AppBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Swagger\Annotations as SWG;

/**
 * @SWG\Info(
 *   version="1.0",
 *   title="Trailburning Journey API",
 *   @SWG\Contact(
 *     name="Matt Allbuery",
 *     email="matt@trailburning.com",
 *     url="http://www.trailburning.com/",
 *   )
 * )
 *
 * @SWG\Swagger(
 *   basePath="/v2",
 *   produces={"application/json"},
 *   consumes={"application/json"},
 * )
 */
class AppBundle extends Bundle
{
	public function boot()
	{
        $this->registerDoctrineTypeHstore();
	}
    
    private function registerDoctrineTypeHstore() 
    {
        $this->container->get('doctrine')->getManager()->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('hstore', 'hstore');
    }
}
