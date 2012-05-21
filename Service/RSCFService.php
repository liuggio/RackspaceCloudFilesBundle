<?php

namespace Liuggio\RackspaceCloudFilesBundle\Service;

/**
 * Description of RackSpaceObject
 *
 * @author liuggio
 */
class RSCFService implements \Liuggio\RackspaceCloudFilesStreamWrapper\RackspaceCloudFilesServiceInterface
{
    private $authentication_service;

    private $connection_class;

    private $servicenet;

    private static $authentication;

    private static $connection;

    private $protocolName;

    private $resource_class;

    private $streamWrapperClass;

    private  $file_type_guesser;

    /**
     *
     * $protocolName,
     * $container_prefix,
     * $authentication,
     * $connection_class,
     * $servicenet
     *
     * @param $authentication_service
     * @param  $connection_service
     * @param  $stream_wrapper_service
     */
    public function __construct($protocol_name, $authentication_service, $connection_class, $servicenet, $stream_wrapper_class, $resource_entity_class, $file_type_guesser)
    {
        $this->protocolName = $protocol_name;
        $this->authentication_service = $authentication_service;
        $this->setConnectionClass($connection_class);
        $this->setServicenet($servicenet);
        $this->streamWrapperClass = $stream_wrapper_class;
        $this->resource_class = $resource_entity_class;
        $this->setFileTypeGuesser($file_type_guesser);
    }

    /**
     * get the RSCF Authentication Service
     *
     * @return authentication
     */
    public function getAuthentication()
    {
        if (!self::$authentication) {
            self::$authentication = $this->getAuthenticationService();
            self::$authentication->authenticate();
        }
        $auth =  self::$authentication;
        return $auth;
    }

    /**
     * get the RSCF Connection Service
     *
     * @return connection
     */
    public function getConnection()
    {
        if (!self::$connection) {
            $connectionClass=  $this->getConnectionClass();
            $auth = $this->getAuthentication();
            self::$connection = new $connectionClass($auth, $this->getServiceNet());
        }
        return self::$connection;
    }

    /**
     * @return string
     */
    public function getProtocolName()
    {
        return $this->protocolName;
    }

    /**
     * @return string
     */
    public function getResourceClass()
    {
        return $this->resource_class;
    }

    /**
     * @return string
     */
    public function getStreamWrapperClass()
    {
        return $this->streamWrapperClass;
    }

    /**
     *
     * @param type $resource
     * @return false|container
     */
    public function getContainerByResource($resource)
    {
        return $resource->getContainer();
    }

    /**
     *
     *
     * @param $resource
     * @return false|object
     */
    public function getObjectByResource($resource)
    {
        $container = $resource->getContainer();
        if ($container) {
            return $resource->getObject();
        } else {
            return false;
        }
    }

    /**
     * @param string $container_name
     * @return \stdClass
     */
    public function apiGetContainer($container_name)
    {
        if (!$this->getConnection()) {
            return false;
        }

        $container = $this->getConnection()->get_container($container_name);
        if (!$container) {
            return false;
        }
        return $container;
    }

    /**
     * @param $container
     * @param string$object_name
     * @return \stdClass
     */
    public function apiGetObjectByContainer($container, $object_name)
    {
        if (!$container) {
            return false;
        }
        return $container->create_object($object_name);
    }

    /**
     *
     * @param string $path
     * @return resource|false
     */
    public function createResourceFromPath($path)
    {
        $resource = $this->getResourceClass();
        $resource = new $resource($path);
        if (!$resource) {
            return false;
        }

        $container = $this->apiGetContainer($resource->getContainerName());
        if (!$container) {
            return false;
        }
        $resource->setContainer($container);
        //create_object but no problem if already exists
        $obj = $this->apiGetObjectByContainer($container, $resource->getResourceName());
        if (!$obj) {
            return false;
        }
        $resource->setObject($obj);

        return $resource;
    }

    /**
     * @param $file_type_guesser
     */
    public function setFileTypeGuesser($file_type_guesser)
    {
        $this->file_type_guesser = $file_type_guesser;
    }

    /**
     * call the worker and guess the mimetype
     * @param string $filename
     * @return string
     */
    public function guessFileType($filename)
    {
        $function = $this->file_type_guesser;
        return $function::guessByFileName($filename);
    }

    public function setAuthenticationService($authentication_service)
    {
        $this->authentication_service = $authentication_service;
    }

    public function getAuthenticationService()
    {
        return $this->authentication_service;
    }

    public function setConnectionClass($connection_class)
    {
        $this->connection_class = $connection_class;
    }

    public function getConnectionClass()
    {
        return $this->connection_class;
    }

    public function setServicenet($servicenet)
    {
        $this->servicenet = $servicenet;
    }

    public function getServicenet()
    {
        return $this->servicenet;
    }

}

 
