<?php

namespace AppBundle;

use Swagger\Annotations as SWG;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * @SWG\Info(
 *   version="2.0",
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
 *   consumes={"application/json","multipart/form-data","application/x-www-form-urlencoded"},
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
