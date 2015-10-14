<?php

namespace AppBundle\Response;

/**
 * Class APIResponseBuilder.
 */
class APIResponseBuilder
{
    /**
     * @param mixed  $body
     * @param string $name
     *
     * @return APIResponse
     */
    public function buildSuccessResponse($body, $name)
    {
        $response = new APIResponse();
        $response->addToBody($body, $name);

        return $response;
    }

    /**
     * @param mixed  $body
     * @param string $name
     *
     * @return APIResponse
     */
    public function buildNotFoundResponse($message)
    {
        $response = new APIResponse(404, 'error');
        $response->addMessage($message);

        return $response;
    }

    /**
     * @param string $statusCode
     * @param string $status
     *
     * @return APIResponse
     */
    public function buildResponse($statusCode, $status)
    {
        $response = new APIResponse($statusCode, $status);

        return $response;
    }
}
