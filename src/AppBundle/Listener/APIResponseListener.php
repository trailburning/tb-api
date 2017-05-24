<?php

namespace AppBundle\Listener;

use AppBundle\Model\APIResponse;
use FOS\RestBundle\View\View;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use JMS\Serializer\SerializationContext;

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
        if (count($apiResponse->getResonseGroups()) > 0) {
            $view->setSerializationContext(SerializationContext::create()->setGroups($apiResponse->getResonseGroups()));
        }
        foreach ($apiResponse->getHeaders() as $name => $value) {
            $view->setHeader($name, $value);
        }
        $event->setControllerResult($view);
    }
}
