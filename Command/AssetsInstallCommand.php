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
        $targetArg = rtrim($input->getArgument('target'), '/');

        $output->writeln('Installing assets to rackspace cloudfiles');

        foreach ($this->getContainer()->get('kernel')->getBundles() as $bundle) {
            if (is_dir($originDir = $bundle->getPath().'/Resources/public')) {
                $bundlesDir = $targetArg.'/bundles/';
                $targetDir  = $bundlesDir.preg_replace('/bundle$/', '', strtolower($bundle->getName()));

                $output->writeln(sprintf('Installing assets for <comment>%s</comment> into <comment>%s</comment>', $bundle->getNamespace(), $targetDir));

                // We use a custom iterator to ignore VCS files
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
        }
    }
}
