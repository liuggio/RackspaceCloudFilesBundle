<?php

namespace Liuggio\RackspaceCloudFilesBundle\Tests\Service;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Liuggio\RackspaceCloudFilesStreamWrapper\RackspaceCloudFilesResource;

/**
 * @author liuggio
 */
class RSCFServiceTest extends WebTestCase
{

    public function getMockService($function = null)
    {
        $service = $this->getMockBuilder('\\Liuggio\\RackspaceCloudFilesBundle\\Service\\RSCFService')
            ->disableOriginalConstructor()
            ->setMethods($function)
            ->getMock();
        return $service;
    }


    public function testApiGetContainer() {
        //we want to asser that the get_container api is called

        $container = new \StdClass();
        $container->name = 'container';

        $connection = $this->getMock("\StdClass", array('get_container'));
        $connection->expects($this->any())
            ->method('get_container')
            ->will($this->returnValue($container));

        $service = $this->getMockService(array('getConnection'));

        $service->expects($this->any())
            ->method('getConnection')
            ->will($this->returnValue($connection));

        $ret = $service->apiGetContainer('container');

        $this->assertEquals($ret, $container);
    }


    public function testApiGetObjectByContainer() {
        //we want to assert that the create_object api is called

        $obj = new \stdClass();
        $obj->name = 'object';

        $container = $this->getMock("\StdClass", array('create_object'));
        $container->expects($this->any())
            ->method('create_object')
            ->will($this->returnValue($obj));

        $service = $this->getMockService();

        $ret = $service->apiGetObjectByContainer($container, 'name');

        $this->assertEquals($ret, $obj);
    }
    

    public function testCreateResourceFromPath() {
        //we want to test that the file is unlinked
        $resourceName = 'js_75a9295_bootstrap-modal_3.js';
        $resourceContainerName = 'liuggio_assetic';
        $path = 'rscf://' . $resourceContainerName . '/' . $resourceName;

        $object = new \StdClass();
        $object->name = 'object';
        $container = new \StdClass();
        $container->name = 'container';

        $resource = new RackspaceCloudFilesResource();
        $resource->setResourceName($resourceName);
        $resource->setContainerName($resourceContainerName);
        $resource->setObject($object);
        $resource->setContainer($container);
        $resource->setCurrentPath($path);

        $service = $this->getMockService(array('getResourceClass','apiGetContainer', 'apiGetObjectByContainer'));

        $service->expects($this->any())
            ->method('getResourceClass')
            ->will($this->returnValue('\\Liuggio\\RackspaceCloudFilesStreamWrapper\\RackspaceCloudFilesResource'));
        $service->expects($this->any())
            ->method('apiGetContainer')
            ->will($this->returnValue($container));
        $service->expects($this->any())
            ->method('apiGetObjectByContainer')
            ->will($this->returnValue($object));


        $ret = $service->createResourceFromPath($path);

        //asserting
        $this->assertEquals($ret, $resource);
    }
    
}

 
