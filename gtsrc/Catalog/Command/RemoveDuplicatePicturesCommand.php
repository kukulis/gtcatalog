<?php
/**
 * RemoveDuplicatePicturesCommand.php
 * Created by Giedrius Tumelis.
 * Date: 2021-02-15
 * Time: 10:38
 */

namespace Gt\Catalog\Command;


use Gt\Catalog\Services\RemoveDuplicatePicturesService;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class RemoveDuplicatePicturesCommand  extends Command
{
    /** @var LoggerInterface */
    private $logger;


    /** @var RemoveDuplicatePicturesService */
    private $removeDuplicatePicturesService;

    /**
     * RemoveDuplicatePicturesCommand constructor.
     * @param LoggerInterface $logger
     * @param RemoveDuplicatePicturesService $removeDuplicatePicturesService
     */
    public function __construct(LoggerInterface $logger, RemoveDuplicatePicturesService $removeDuplicatePicturesService)
    {
        $this->logger = $logger;
        $this->removeDuplicatePicturesService = $removeDuplicatePicturesService;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Removes duplicate pictures from products' );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
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

        $count = $this->removeDuplicatePicturesService->removeDuplicates();

        $output->writeln('Removed '.$count. ' duplicates');
        return 0;
    }
}