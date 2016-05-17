<?php

namespace AppBundle\Model;

use JMS\Serializer\Annotation as Serializer;

/**
 */
class APIResponse
{
    
    /**
     * @var string
     * @Serializer\Exclude
     */
    private $statusCode;

    /**
     * @var array
     */
    private $body;

    /**
     * @var array
     */
    private $meta;

    /**
     * @var array
     */
    private $messages;

    /**
     * @var array
     * @Serializer\Exclude
     */
    private $headers;

    public function __construct($statusCode = 200)
    {
        $this->setStatusCode($statusCode);
        $this->headers = [];
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
     * @param string $data
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
     * @param string $message
     */
    public function addheader($name, $value)
    {
        $this->headers[$name] = $value;
    }

    public function getHeaders()
    {
        return $this->headers;
    }
}
