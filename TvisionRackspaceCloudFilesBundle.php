<?php

namespace Tvision\RackspaceCloudFilesBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class TvisionRackspaceCloudFilesBundle extends Bundle
{
    /**
     * Boots the Bundle.
     */
    public function boot()
    {
        parent::boot();
        $swc = $this->container->getParameter('tvision_rackspace_cloud_files.stream_wrapper.class');
        $swc::$service =  $this->container->get('tvision_rackspace_cloud_files.service');

        if($this->container->getParameter('tvision_rackspace_cloud_files.stream_wrapper.register')) {
            $swc::registerStreamWrapperClass($this->container->getParameter('tvision_rackspace_cloud_files.stream_wrapper.protocol_name'));
        }
    }

    /**
     * Shutdowns the Bundle.
     */
    public function shutdown()
    {
        if($this->container->getParameter('tvision_rackspace_cloud_files.stream_wrapper.register')) {
            $swc = $this->container->getParameter('tvision_rackspace_cloud_files.stream_wrapper.class');
            $swc::unRegisterStreamWrapperClass();
        }
        parent::shutdown();
    }
}
