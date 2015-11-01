<?php

namespace AppBundle\Services;

use Symfony\Component\Config\ConfigCache;
use Symfony\Component\Config\Resource\FileResource;

class SwaggerBridge
{

    /**
     * @var string
     */
    private $rootDir;
    
    /**
     * @var string
     */
    private $env;

    /**
     * @var bool
     */
    private $isDebug;

    /**
     * @param string $rootDir
     * @param string $env
     * @param bool $isDebug
     */
    function __construct($rootDir, $env, $isDebug) {
        $this->rootDir = $rootDir;
        $this->env = $env;
        $this->isDebug = $isDebug;
    }

    /**
     * @return string
     */
    public function generateJson()
    {
        $configCache = new ConfigCache($this->getCachePath(), $this->isDebug);
        if ($configCache->isFresh() && $this->isDebug === false) {
            return file_get_contents($this->getCachePath());
        }

        $resource = new FileResource($this->getCachePath());
        $paths = [
            $this->rootDir . '/../src',
        ];
        $options = [];
        $swagger = @\Swagger\scan($paths, $options);
        if ($this->env === 'dev') {
            $swagger->basePath = '/app_dev.php' . $swagger->basePath;
        }

        $json = $swagger->__toString();
        $configCache->write($json, [$resource]);

        return $json;
    }

    public function getCachePath()
    {
        return $this->rootDir . '/cache/' . $this->env . '/swagger.json';
    }
}
