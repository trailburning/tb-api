<?php
namespace AppBundle\Response;

use JMS\Serializer\Annotation\Exclude;

/**
 */
class ApiResponse
{   
    const STATUS_SUCCESS = 'success';
    
    const STATUS_ERROR = 'error';
    
    /**
     * @var string
     */
    private $status;
    
    /**
     * @var array
     */
    private $body;
    
    /**
     * @var array
     */
    private $meta;
    
    public function __construct($body = null, $status = self::STATUS_SUCCESS, $meta = null) 
    {
        $this->setStatus($status);
        $this->setBody($body);
        $this->setMeta($meta);
    }
    
    /**
     *
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
     * @param string $body 
     */
    public function setBody($body) 
    {
        $this->body = $body;
    }
    
    /**
     * @param string $meta 
     */
    public function setMeta($meta) 
    {
        $this->meta = $meta;
    }
}
