<?php

namespace Liuggio\RackspaceCloudFilesBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\Finder\Finder;

/**
 * This command installs assets directly to rackspace cloudfiles container
 *
 * @author Benjamin Dulau <benjamin.dulau@gmail.com>
 */
class AssetsInstallCommand extends ContainerAwareCommand
{
    /**
     * @see Command
     */
    protected function configure()
    {
        $this
            ->setName('rscf:assets:install')
            ->setDefinition(array(
                new InputArgument('localDir', InputArgument::REQUIRED, 'path/to/dir, all , bundle name'),
                new InputArgument('target', InputArgument::REQUIRED, 'The target directory'),
                new InputOption('verbose', 'v', InputOption::VALUE_NONE, 'Verbose mode'),
            ))
            ->setDescription('Installs bundles web assets under a rackspace cloudfiles container')
            ->setHelp(<<<EOT
The <info>%command.name%</info> command installs bundle assets into a given
rackspace cloudfiles container.

<info>php %command.full_name% rscf://my_container/path</info>

The "Resources/public" directory files of each bundle will be copied into the
given rackspace cloudfiles container virtual path.

To view every file that is being created, you can use the
<info>--verbose</info> option:

<info>php %command.full_name% rscf://my_container/path --verbose</info>

EOT
            )
        ;
    }

    /**
     * @see Command
     *
     * @throws \InvalidArgumentException When the target directory does not exist
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
/*        print_r(stream_get_wrappers());*/
        $targetArg = rtrim($input->getArgument('target'), '/');
        $localDir = rtrim($input->getArgument('localDir'), '/');

        $output->writeln('Installing assets to rackspace cloudfiles');

        $bundles = $this->getContainer()->get('kernel')->getBundles();
        $bundlesName =  $this->getContainer()->getParameter('kernel.bundles');
        //$output->writeln(sprintf('Installing assets for <comment>%s</comment> ', $localDir));

        if (in_array($localDir, $bundlesName)) {
            $output->writeln(sprintf('Installing assets for bundle <comment>%s</comment> ', $localDir));
            $bundlePath = $this->getBundlePath($localDir);
            $targetDir = $this->getBundleTargetDir($targetArg, $bundlePath);
            $originDir = $bundlePath.'/Resources/public';
            $this->flush($input, $output, $originDir, $targetDir);
        }
        elseif(is_dir($localDir)){
            $output->writeln(sprintf('Installing assets for dir <comment>%s</comment> ', $localDir));
            $targetDir = $this->getFileSystemTargetDir($targetArg, $localDir);
            $originDir = $localDir;
            $this->flush($input, $output, $originDir, $targetDir);
        }
        elseif($localDir == "all") {
            $output->writeln(sprintf('Installing assets for all bundles '));
            foreach($bundles as $bundle){
                $targetDir = $this->getBundleTargetDir($targetArg, $bundle->getPath());
                $this->flush($input, $output, $bundle->getPath().'/Resources/public', $targetDir);
            }
        }
        else {
            $output->writeln(sprintf('localDir option does not exist!available options are: path/to/dir, src/, bundle name'));
        }
    }



    protected function flush($input, $output, $originDir, $targetDir){
        $iterator = Finder::create()->in($originDir);
        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $targetFile = $targetDir.'/'.$file->getRelativePathname();
                if ($input->getOption('verbose')) {
                    $output->writeln(sprintf(
                            '<comment>%s</comment> <info>[file+]</info> %s',
                            date('H:i:s'),
                            $targetFile
                        ));
                }
                 if (false === @file_put_contents($targetFile, file_get_contents($file))) {
                    throw new \RuntimeException('Unable to write file '.$targetFile);
                }
            }
        }
    }


    protected function getBundleTargetDir($targetArg, $bundlePath){
        if (is_dir($bundlePath.'/Resources/public')) {
            $bundlesDir = $targetArg.'/bundles/';
            $targetDir  = $bundlesDir;
            return $targetDir;
        }
        else {
            return false;
        }
    }

    protected function getFileSystemTargetDir($targetArg, $dir){
        if (is_dir($dir)) {
            $targetDir  = $targetArg."/".$dir;
            return $targetDir;
        }
        else {
            return false;
        }
    }

    protected function getBundlePath($bundleName){
        $path = $this->getContainer()->get('kernel')->locateResource('@'.$bundleName);
        return $path;
    }


}
