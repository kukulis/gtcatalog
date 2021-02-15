<?php
/**
 * AutoAssignCustomsNumbersCommand.php
 * Created by Giedrius Tumelis.
 * Date: 2021-01-06
 * Time: 09:42
 */

namespace Gt\Catalog\Command;


use Gt\Catalog\Services\AutoAssignCustomsNumbersService;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class AutoAssignCustomsNumbersCommand  extends Command
{
    /** @var LoggerInterface */
    private $logger;

    /** @var AutoAssignCustomsNumbersService */
    private $autoAssignCustomsNumbersService;

    /**
     * AutoAssignCustomsNumbersCommand constructor.
     * @param LoggerInterface $logger
     * @param AutoAssignCustomsNumbersService $autoAssignCustomsNumbersService
     */
    public function __construct(LoggerInterface $logger, AutoAssignCustomsNumbersService $autoAssignCustomsNumbersService)
    {
        $this->logger = $logger;
        $this->autoAssignCustomsNumbersService = $autoAssignCustomsNumbersService;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Assigns customs codes to  products, taking them from classificators "types"' );
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

        $count=$this->autoAssignCustomsNumbersService->autoAssignCustomsNumbers();

        $output->writeln('Imported '.$count. ' customs numbers objects' );

        if ( $count > 0 ) {
            return 0;
        }
        else {
            return -1;
        }
    }

}