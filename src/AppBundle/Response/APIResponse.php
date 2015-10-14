<?php

namespace AppBundle\Response;

/**
 */
class APIResponse
{
    const STATUS_SUCCESS = 'success';

    const STATUS_ERROR = 'error';

    /**
     * @var string
     */
    private $status;

    /**
     * @var string
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

    public function __construct($statusCode = 200, $status = self::STATUS_SUCCESS)
    {
        $this->setStatusCode($statusCode);
        $this->setStatus($status);
    }

    /**
     * @param string $status
     */
    public function setStatus($status)
    {
        if (in_array($status, [self::STATUS_SUCCESS, self::STATUS_ERROR]) === false) {
            throw new \Exception(sprintf('Invalid status: %s', $status));
        }

        $this->status = $status;
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
     * @param string $body
     */
    public function setBody($body)
    {
        $this->body = $body;
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
        if ($this->messages === null) {
            $this->messages[] = $message;
        }
    }
}
