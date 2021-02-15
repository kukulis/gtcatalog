<?php
/**
 * RemoveUnassignedPicturesCommand.php
 * Created by Giedrius Tumelis.
 * Date: 2021-02-15
 * Time: 12:33
 */

namespace Gt\Catalog\Command;


use Gt\Catalog\Services\RemoveUnassignedPicturesService;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveUnassignedPicturesCommand   extends Command
{
    const REMOVE='remove';

    /** @var LoggerInterface */
    private $logger;

    /** @var RemoveUnassignedPicturesService */
    private $removeUnassignedPicturesService;

    /**
     * RemoveUnassignedPicturesCommand constructor.
     * @param LoggerInterface $logger
     * @param RemoveUnassignedPicturesService $removeUnassignedPicturesService
     */
    public function __construct(LoggerInterface $logger, RemoveUnassignedPicturesService $removeUnassignedPicturesService)
    {
        $this->logger = $logger;
        $this->removeUnassignedPicturesService = $removeUnassignedPicturesService;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Removes duplicate pictures from products' );
        $this->addArgument(self::REMOVE, InputArgument::REQUIRED, 'Ar trinti surastus paveikslėlius' );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $remove = $input->getArgument(self::REMOVE);

        $lineFormater =
            new LineFormatter(
                null,
                null,
                true,
                true
            );

        $out = new StreamHandler(STDOUT, Logger::DEBUG);
        $out->setFormatter($lineFormater);
        $this->logger->pushHandler($out);

        $count = $this->removeUnassignedPicturesService->findAndRemoveUnassignedPictures($remove==1);

        $output->writeln('Removed '.$count. ' duplicates');
        return 0;
    }
}