<?php

namespace Liuggio\RackspaceCloudFilesBundle\Service;

use OpenCloud\Rackspace;
use OpenCloud\OpenStack;
use OpenCloud\ObjectStore;

/**
 *
 * @author toretto460
 */
class RackspaceApi
{
    private static $connection;

    private $endpoint;

    private $username;

    private $apiKey;

    private $connectionClass;

    private $containerName;

    private $serviceName = 'cloudFiles';

    private $region = 'LON';

    private $urlType = 'publicURL';

    public function __construct($connectionClass, $endPoint, $username, $apiKey, $containerName, $region='LON')
    {
        $this->connectionClass = $connectionClass;
        $this->endpoint = $endPoint;
        $this->username = $username;
        $this->apiKey = $apiKey;
        $this->containerName = $containerName;
        $this->region = $region;
    }

    /**
     * Return the OpenStack object.
     *
     * @return OpenStack
     */
    public function connect()
    {
        if( !self::$connection ) {
            $credential = array(
                'username'  => $this->username,
                'apiKey'    => $this->apiKey
            );
            self::$connection = new Rackspace($this->endpoint, $credential);
        }
        return self::$connection;

    }

    /**
     * @return OpenStack
     */
    public function getConnection()
    {
        return $this->connect();
    }

    /**
     * Set the default connection values for cloudFiles service.
     *
     * @return void
     */
    private function setDefaults()
    {
        $this->getConnection()->setDefaults('ObjectStore', $this->serviceName, $this->region, $this->urlType);
    }

    /**
     * Get the ObjectStore.
     *
     * @return \OpenCloud\ObjectStore
     */
    public function getObjectStore()
    {
        $this->setDefaults();
        return $this->getConnection()->ObjectStore();
    }

    /**
     * Get the container.
     *
     * @param String|null $container_name
     *
     * @return \OpenCloud\ObjectStore\Container
     */
    public function getContainer($container_name = null)
    {
        if( is_null($container_name)) {
            $container_name = $this->containerName;
        }
        return $this->getObjectStore()->Container($container_name);
    }
}

 
