<?php

namespace Tvision\RackspaceCloudFilesBundle\Command;

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
                new InputArgument('filter', InputArgument::OPTIONAL, 'path filter'),
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
        $targetArg = rtrim($input->getArgument('target'), '/');
        $localDir = rtrim($input->getArgument('localDir'), '/');

        if($input->getArgument('filter')){
            $filter = $input->getArgument('filter');
        }
        else {
            $filter = false;
        }

        $output->writeln('Installing assets to rackspace cloudfiles');

        $bundles = $this->getContainer()->get('kernel')->getBundles();
        $bundlesName =  $this->getContainer()->getParameter('kernel.bundles');

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
            $this->flush($input, $output, $originDir, $targetDir, $filter);
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

    /**
     * @param $input
     * @param $output
     * @param $originDir
     * @param $targetDir
     * @param bool $filter
     */
    protected function flush($input, $output, $originDir, $targetDir, $filter = false){
        if(is_dir($originDir)) {
            $iterator = Finder::create()->in($originDir);
            foreach ($iterator as $file) {
                if ($file->isFile()) {
                    $targetDir = $filter?$this->filterLocalDir($originDir, $filter):$targetDir;
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
    }

    /**
     * @param $localDir
     * @param $filter
     * @return bool|mixed
     */
    protected function filterLocalDir($localDir, $filter){
        if(strstr($localDir, $filter)){
            return str_replace($filter, '', $localDir);
        }
        else {
            return false;
        }

    }

    /**
     * @param $targetArg
     * @param $bundlePath
     * @return bool|string
     */
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

    /**
     * @param $targetArg
     * @param $localDir
     * @return string
     */
    protected function getFileSystemTargetDir($targetArg, $localDir){
        if (is_dir($localDir)) {
            $targetDir  = $targetArg."/".$localDir;
            return $targetDir;
        }
        else {
            return false;
        }
    }

    /**
     * @param $bundleName
     * @return mixed
     */
    protected function getBundlePath($bundleName){
        $path = $this->getContainer()->get('kernel')->locateResource('@'.$bundleName);
        return $path;
    }


}
