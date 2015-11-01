<?php

namespace AppBundle\Listener;

use AppBundle\Response\APIResponse;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

/**
 * Class APIResponseListener.
 */
class APIResponseListener
{
    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function onKernelView(GetResponseForControllerResultEvent $event)
    {
        $apiResponse = $event->getControllerResult();
        if (!$apiResponse instanceof APIResponse) {
            return;
        }

        $view = new View($apiResponse, $apiResponse->getStatusCode());
        foreach ($apiResponse->getHeaders() as $name => $value) {
            $view->setHeader($name, $value);
        }
        $event->setControllerResult($view);
    }
}
