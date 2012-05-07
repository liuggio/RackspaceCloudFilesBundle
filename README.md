Introduction
------------

Rackspace Cloud Files bundle is a simple and easy way to use the namespaced version of php-cloudfiles with Symfony2 applications,

but it has also some facilities for handle the static file with the rackspace cloud files.


[![Build Status](https://secure.travis-ci.org/liuggio/RackspaceCloudFilesBundle.png)](http://travis-ci.org/liuggio/RackspaceCloudFilesBundle)


see the blog post for more detail

[http://www.welcometothebundle.com/symfony2-assets-on-rackspace-cloud-files/](http://www.welcometothebundle.com/symfony2-assets-on-rackspace-cloud-files)


Installation
-----------

see the blog post for more detail

deps:

```
[php-cloudfiles]
    git=git://github.com/rackspace/php-cloudfiles.git
    target=/rackspace/php-cloudfiles

[RackspaceCloudFilesBundle]
    git=https://github.com/liuggio/RackspaceCloudFilesBundle.git
    target=/bundles/Liuggio/RackspaceCloudFilesBundle

[RackspaceCloudFilesStreamWrapper]
    git=https://github.com/liuggio/RackspaceCloudFilesStreamWrapper.git
    target=liuggio-rcfs-streamwrapper

```

app/autoload.php

```
$loader->registerNamespaces(array(
    //other namespaces
    'Liuggio\\RackspaceCloudFilesStreamWrapper' =>  __DIR__.'/../vendor/liuggio-rscf-streamwrapper/src',
    'Liuggio\\RackspaceCloudFilesBundle'        =>  __DIR__.'/../vendor/bundles',
  ));

require_once __DIR__.'/../vendor/rackspace/php-cloudfiles/cloudfiles.php';
```

app/AppKernel.php

```
public function registerBundles()
{
    return array(
        //other bundles
        new \Liuggio\RackspaceCloudFilesBundle\LiuggioRackspaceCloudFilesBundle(),
    );
    ...
```

## Configuration

app/config/config.yml

```
# Escape Rackspace Cloud Files configuration

liuggio_rackspace_cloud_files:
    service_class: Liuggio\RackspaceCloudFilesStreamWrapper\StreamWrapper\RackspaceCloudFilesStreamWrapper
    stream_wrapper:
        register: true  # do you want to register stream wrapper?
#        protocol_name: rscf
#        class: Liuggio\StreamWrapper\RackspaceCloudFilesStreamWrapper
    auth:
        authentication_class: CF_Authentication
        connection_class: CF_Connection
        username: YOUR-USERNAME
        api_key: YOUR-API-KEY
        host: https://lon.auth.api.rackspacecloud.com # or usa
        #servicenet: true
```

## Service(s)

Get the Authentication objects to work with:

```
$auth = $this->get('liuggio_rackspace_cloud_files.service')->getAutentication();
$conn = $this->get('liuggio_rackspace_cloud_files.service')->getConnection();

```

## Usage example without assetic

```

$conn = $this->get('liuggio_rackspace_cloud_files.service');
$container = $conn->get_container('container-name');

or

$container = $this->get('liuggio_rackspace_cloud_files.service')->apiGetContainer('container-name');

echo "<pre>";
print_r($container->list_objects());
echo "</pre>";
```


## Usage example with assetic










Requirements
------------

- PHP > 5.3.0

- rackspace/php-cloudfiles.git

- liuggio/RackspaceCloudFilesStreamWrapper

- Symfony2


Contribute
----------

Please feel free to use the Git issue tracking to report back any problems or errors. You're encouraged to clone the repository and send pull requests if you'd like to contribute actively in developing the library.
than add your name to this file under the contributor section



Contributor
------------

- thanks for cystbear for the tips

- the bundle is a reengeneering of the escapestudios/EscapeRackspaceCloudFilesBundle


1. liuggio


License
-------

Copyright (C) 2012 by liuggio

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated documentation files (the "Software"), to deal in the Software without restriction, including without limitation the rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.

