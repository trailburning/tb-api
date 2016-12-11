<?php

namespace AppBundle\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 * @Serializer\ExclusionPolicy("all")
 */
class APIResponse
{
    
    /**
     * @var string
     */
    private $statusCode;

    /**
     * @var array
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     *
     */
    private $body;

    /**
     * @var array
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $meta;

    /**
     * @var array
     * @Serializer\Expose
     * @Serializer\Groups({"raceEvent", "user"})
     */
    private $messages;

    /**
     * @var array
     */
    private $headers;

    /**
     * @var array
     */
    private $responseGroups;

    public function __construct($statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        $this->headers = [];
        $this->responseGroups = [];
    }

    /**
     * @param int $statusCode
     */
    public function setStatusCode($statusCode)
    {
        $this->statusCode = $statusCode;
    }

    /**
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * @param mixed $body
     */
    public function setBody($body)
    {
        $this->body = $body;
    }

    /**
     * @return mixed
     */
    public function getBody()
    {
        return $this->body;
    }

    /**
     * @param $body
     * @param $name
     */
    public function addToBody($body, $name)
    {
        if (!is_array($this->body)) {
            $this->body = [];
        }

        $this->body[$name] = $body;
    }

    /**
     * @param string $meta
     */
    public function setMeta($meta)
    {
        $this->meta = $meta;
    }

    /**
     * @param string $message
     */
    public function addMessage($message)
    {
        $this->messages[] = $message;
    }

    /**
     * @param $name
     * @param $value
     */
    public function addheader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    /**
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }

    /**
     * @return array
     */
    public function getResonseGroups()
    {
        return $this->responseGroups;
    }

    /**
     * @param string $responseGroup
     */
    public function addResponseGroup(string $responseGroup)
    {
        $this->responseGroups[] = $responseGroup;
    }

}
