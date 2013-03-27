<?php

namespace Liuggio\RackspaceCloudFilesBundle\Service;

use Liuggio\RackspaceCloudFilesBundle\Service\RackspaceApi;
use OpenCloud\ObjectStore\Container;
use OpenCloud\ObjectStore\DataObject;
use Liuggio\RackspaceCloudFilesStreamWrapper\RackspaceCloudFilesServiceInterface;

/**
 * Description of RackSpaceObject
 *
 * @author liuggio
 */
class RSCFService implements RackspaceCloudFilesServiceInterface
{
    private $rackspaceService;

    private $protocolName;

    private $resource_class;

    private $streamWrapperClass;

    private  $file_type_guesser;


    public function __construct($protocol_name, RackspaceApi $rackspaceService, $stream_wrapper_class, $resource_entity_class, $file_type_guesser)
    {
        $this->protocolName = $protocol_name;
        $this->rackspaceService = $rackspaceService;
        $this->streamWrapperClass = $stream_wrapper_class;
        $this->resource_class = $resource_entity_class;
        $this->setFileTypeGuesser($file_type_guesser);
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
     *
     * @return Container|false
     */
    public function apiGetContainer($container_name)
    {
        $container = $this->getRackspaceService()->getContainer($container_name);
        if (!$container) {
            return false;
        }
        return $container;
    }


    /**
     * @param \OpenCloud\ObjectStore\Container $container
     * @param $objectData
     *
     * @return DataObject
     */
    public function apiGetObjectByContainer(Container $container, $objectData)
    {
        if (!$container) {
            return false;
        }
        $object = $container->DataObject();
        $object->name = $objectData['name'];
        $object->content_type = $objectData['content_type'];

        return $object;
    }

    /**
     *
     * @param string $path
     *
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

    public function getRackspaceService()
    {
        return $this->rackspaceService;
    }

}

 
