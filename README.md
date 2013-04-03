Introduction
------------


Rackspace Cloud Files bundle is a simple and easy way to use the namespaced version of php-cloudfiles with Symfony2 applications,

but it has also some facilities for handle the static file with the rackspace cloud files.

This Bundle borns as fork of the escapestudios/EscapeRackspaceCloudFilesBundle, now these two bundles are very different.

[![Build Status](https://secure.travis-ci.org/liuggio/RackspaceCloudFilesBundle.png)](http://travis-ci.org/liuggio/RackspaceCloudFilesBundle)


see the blog post for more detail

[http://www.welcometothebundle.com/symfony2-assets-on-rackspace-cloud-files/](http://www.welcometothebundle.com/symfony2-assets-on-rackspace-cloud-files)


Installation (old school)
-------------------------------

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
    target=liuggio-rscf-streamwrapper

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
        new Liuggio\RackspaceCloudFilesBundle\LiuggioRackspaceCloudFilesBundle(),
    );
    ...
```

Installation Composer
-------------------------------

* 1 First, add the dependent bundles to the vendor/bundles directory. Add the following lines to the composer.json file

```
    "require": {
    # ..
    "liuggio/rackspace-cloud-files-bundle": ">=2.0",
    # ..
    }
```

* 2 Then run `composer install`


* 3 Then add in your `app/AppKernel`

``` yaml

 class AppKernel extends Kernel
 {
     public function registerBundles()
     {
         $bundles = array(
         // ...
            new Liuggio\RackspaceCloudFilesBundle\LiuggioRackspaceCloudFilesBundle(),
         // ...

```


## Configuration

app/config/config.yml

```
#  Rackspace Cloud Files configuration

liuggio_rackspace_cloud_files:
    service_class: Liuggio\RackspaceCloudFilesStreamWrapper\StreamWrapper\RackspaceCloudFilesStreamWrapper
    stream_wrapper:
        register: true  # do you want to register stream wrapper?
#        protocol_name: rscf
#        class: Liuggio\StreamWrapper\RackspaceCloudFilesStreamWrapper
    auth:
        username: YOUR-USERNAME
        api_key: YOUR-API-KEY
        host: https://lon.auth.api.rackspacecloud.com # or usa
        container_name: YOUR-CONTAINER-NAME
        region: 'LON' # or DFW or ORD
```

## Service(s)

Get the Rackspace service to work with:

```
$auth = $this->get('liuggio_rackspace_cloud_files.service')

```

## Usage example without assetic

```

$conn = $this->get('liuggio_rackspace_cloud_files.service');
$container = $conn->apiGetContainer('container-name');

or

$container = $this->get('liuggio_rackspace_cloud_files.service')->apiGetContainer('container-name');

echo "<pre>";
printf("Container %s has %d object(s) consuming %d bytes\n",
    $container->name, $container->count, $container->bytes);
echo "</pre>";
```


## Usage example with assetic


see

http://www.welcometothebundle.com/symfony2-assets-on-rackspace-cloud-files/


## Installing bundles assets (public directory) to cloudfiles with `rscf:assets:install` special console command

```
app/console rscf:assets:install rscf://my_container/my/path
```

This will copy assets just like the `assets:install` command would but directly to cloudfiles.
**Note**: For those wondering why this command could be needed, note that assetic mainly handles js/css assets, and when
 not using the cssembed filter, you still need to install images to your cloudfiles container. This command prevent you
 from having to do that by hand.


## Installing application assets (public directory) to cloudfiles with `assetic:install` special console command

add this into the config.yml

```
assetic:
    debug: false
    use_controller: false
    write_to: rsfc://%rackspace_container_name%
```

Type to the console

```
app/console assetic:dump
```

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

2. benjamindulau


License
-------

This bundle is under the MIT license. See the complete license in the bundle:

    Resources/meta/LICENSE
