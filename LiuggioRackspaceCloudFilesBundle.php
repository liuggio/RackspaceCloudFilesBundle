<?php

namespace Liuggio\RackspaceCloudFilesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class LiuggioRackspaceCloudFilesBundle extends Bundle
{
    /**
     * Boots the Bundle.
     */
    public function boot()
    {
        parent::boot();
        $swc = $this->container->getParameter('liuggio_rackspace_cloud_files.stream_wrapper.class');
        $swc::$service =  $this->container->get('liuggio_rackspace_cloud_files.service');

        if($this->container->getParameter('liuggio_rackspace_cloud_files.stream_wrapper.register')) {
            $swc::registerStreamWrapperClass($this->container->getParameter('liuggio_rackspace_cloud_files.stream_wrapper.protocol_name'));
        }
    }

    /**
     * Shutdowns the Bundle.
     */
    public function shutdown()
    {
        if($this->container->getParameter('liuggio_rackspace_cloud_files.stream_wrapper.register')) {
            $swc = $this->container->getParameter('liuggio_rackspace_cloud_files.stream_wrapper.class');
            $swc::unRegisterStreamWrapperClass();
        }
        parent::shutdown();
    }
}
