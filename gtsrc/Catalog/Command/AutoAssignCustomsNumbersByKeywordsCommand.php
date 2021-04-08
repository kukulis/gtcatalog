<?php
/**
 * AutoAssignCustomsNumbersByKeywordsCommand.php
 * Created by Giedrius Tumelis.
 * Date: 2021-04-08
 * Time: 08:38
 */

namespace Gt\Catalog\Command;


use Gt\Catalog\Services\AutoAssignCustomsNumbersByKeywordsService;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AutoAssignCustomsNumbersByKeywordsCommand   extends Command
{
    /** @var LoggerInterface */
    private $logger;

    /** @var AutoAssignCustomsNumbersByKeywordsService */
    private $autoAssignCustomsNumbersByKeywordsService;

    /**
     * AutoAssignCustomsNumbersByKeywordsCommand constructor.
     * @param LoggerInterface $logger
     * @param AutoAssignCustomsNumbersByKeywordsService $autoAssignCustomsNumbersByKeywordsService
     */
    public function __construct(LoggerInterface $logger, AutoAssignCustomsNumbersByKeywordsService $autoAssignCustomsNumbersByKeywordsService)
    {
        $this->logger = $logger;
        $this->autoAssignCustomsNumbersByKeywordsService = $autoAssignCustomsNumbersByKeywordsService;

        parent::__construct();
    }


    protected function configure()
    {
        $this->setDescription('Assigns customs codes to  products, taking them from keywords table' );
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

        $count = $this->autoAssignCustomsNumbersByKeywordsService->autoAssign();

        $output->writeln('Imported '.$count. ' customs numbers objects' );

        if ( $count > 0 ) {
            return 0;
        }
        else {
            return -1;
        }
    }
}