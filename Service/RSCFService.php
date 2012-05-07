<?php

namespace Liuggio\RackspaceCloudFilesBundle\Service;

/**
 * Description of RackSpaceObject
 *
 * @author liuggio
 */
class RSCFService implements  \Liuggio\RackspaceCloudFilesStreamWrapper\RackspaceCloudFilesServiceInterface
{    
    private $authentication;
    
    private $connection;
    
    private $protocolName;

    private $resource_class;

    private $streamWrapperClass;


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
    public function __construct($protocol_name, $container_prefix, $authentication_service, $connection_class, $servicenet, $stream_wrapper_class, $resource_entity_class) {

        $this->protocolName = $protocol_name;                
        $authentication_service->authenticate();
        $this->authentication = $authentication_service;
        $this->connection = new $connection_class($this->authentication, $servicenet);
        $this->streamWrapperClass = $stream_wrapper_class;

        $this->resource_class =$resource_entity_class;
    }
    /**
     * get the RSCF Authentication Service
     * 
     * @return authentication
     */
    public function getAuthentication() {
        return $this->authentication;
    }
    /**
     * get the RSCF Connection Service
     * 
     * @return connection
     */
    public function getConnection() {
        return $this->connection;
    }
    /**
     * @return string
     */
    public function getProtocolName() {
        return $this->protocolName;
    }
    /**
     * @return string
     */
    public function getResourceClass() {
        return $this->resource_class;
    }
    /**
     * @return string
     */
    public function getStreamWrapperClass() {
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
    public function getObjectByResource($resource) {
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
    public function apiGetContainer($container_name) {
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
    public function apiGetObjectByContainer($container, $object_name) {
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
    public function createResourceFromPath($path) {
        $resource = $this->getResource_class();
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
    
}

 
