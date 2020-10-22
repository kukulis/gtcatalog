<?php
/**
 * ImportTmpDataFromRestCommand.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-21
 * Time: 15:08
 */

namespace Gt\Catalog\Command\Legacy;


use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Class ImportTmpDataFromRestCommand
 * @package Gt\Catalog\Command
 *
 * We assume that there is a table tmp_skus.
 *
 */
class ImportTmpDataFromRestCommand extends Command
{
    protected function configure()
    {
        $this->setDescription('Imports tmp data from the legacy rest' );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('TODO import tmp data from rest command');
        return 0;
    }
}