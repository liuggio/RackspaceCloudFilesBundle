<?php

namespace Liuggio\RackspaceCloudFilesBundle\Service;

use Liuggio\RackspaceCloudFilesBundle\Service\RackspaceApi;
use \OpenCloud\ObjectStore\Container;

/**
 * Description of RackSpaceObject
 *
 * @author liuggio
 */
class RSCFService implements \Liuggio\RackspaceCloudFilesStreamWrapper\RackspaceCloudFilesServiceInterface
{
    private $rackspaceService;

    private $connection_class;

    private $servicenet;

//    private static $connection;

    private $protocolName;

    private $resource_class;

    private $streamWrapperClass;

    private  $file_type_guesser;


    public function __construct($protocol_name, RackspaceApi $rackspaceService, $connection_class, $servicenet, $stream_wrapper_class, $resource_entity_class, $file_type_guesser)
    {
        $this->protocolName = $protocol_name;
        $this->rackspaceService = $rackspaceService;
        $this->setConnectionClass($connection_class);
        $this->setServicenet($servicenet);
        $this->streamWrapperClass = $stream_wrapper_class;
        $this->resource_class = $resource_entity_class;
        $this->setFileTypeGuesser($file_type_guesser);
    }

//    /**
//     * get the RSCF Connection Service
//     *
//     * @return connection
//     */
//    public function getConnection()
//    {
//        if (!self::$connection) {
//            $connectionClass=  $this->getConnectionClass();
//            $auth = $this->getAuthentication();
//            self::$connection = new $connectionClass($auth, $this->getServiceNet());
//        }
//        return self::$connection;
//    }

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

        $container = $this->getRackspaceService()->getContainer($container_name);
        if (!$container) {
            return false;
        }
        return $container;
    }


    /**
     * @param \OpenCloud\ObjectStore\Container $container
     * @param $objectData
     * @param $pathLocalFile
     *
     * @return bool|\stdClass
     */
    public function apiGetObjectByContainer(Container $container, $objectData, $pathLocalFile)
    {
        if (!$container) {
            return false;
        }
        $object = $container->DataObject();
        return $object->Create($objectData, $pathLocalFile);
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

        $container = $this->getRackspaceService()->getContainer();
        if (!$container) {
            return false;
        }
        $resource->setContainer($container);
        //create_object but no problem if already exists
        $objectData = array(
            'name'  => $resource->getResourceName(),
            'content_type' => $this->guessFileType($path),
        );

        $obj = $this->apiGetObjectByContainer($container, $objectData, $path);
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

//    public function setAuthenticationService($authentication_service)
//    {
//        $this->authentication_service = $authentication_service;
//    }

    public function getRackspaceService()
    {
        return $this->rackspaceService;
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

 
