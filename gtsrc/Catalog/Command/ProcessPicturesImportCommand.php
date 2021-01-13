<?php
/**
 * ProcessPicturesImportCommand.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-20
 * Time: 16:57
 */

namespace Gt\Catalog\Command;


use Gt\Catalog\Services\ImportPicturesService;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ProcessPicturesImportCommand extends Command
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ImportPicturesService */
    private $importPicturesService;

    /**
     * ProcessPicturesImportCommand constructor.
     * @param LoggerInterface $logger
     * @param ImportPicturesService $importPicturesService
     */
    public function __construct(LoggerInterface $logger, ImportPicturesService $importPicturesService)
    {
        $this->logger = $logger;
        $this->importPicturesService = $importPicturesService;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Handles import pictures jobs' );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln('process jobs called' );

        $this->importPicturesService->handleJobs();
        $output->writeln('process jobs finished' );
        return 0;
    }
}