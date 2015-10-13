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
 *   basePath="/",
 *   schemes={"http"},
 *   produces={"application/json"},
 *   consumes={"application/json"},
 * )
 */
class AppBundle extends Bundle
{
    
}
