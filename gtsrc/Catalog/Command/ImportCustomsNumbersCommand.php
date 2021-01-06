<?php
/**
 * ImportCustomsNumbersCommand.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-18
 * Time: 13:35
 */

namespace Gt\Catalog\Command;


use Gt\Catalog\Services\ImportCustomsNumbersService;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ImportCustomsNumbersCommand extends Command
{
    const FILE = 'file';

    /** @var LoggerInterface */
    private $logger;

    /** @var ImportCustomsNumbersService */
    private $importCustomsNumbersService;

    /**
     * ImportCustomsNumbersCommand constructor.
     * @param LoggerInterface $login
     * @param ImportCustomsNumbersService $importCustomsNumbersService
     */
    public function __construct(LoggerInterface $login, ImportCustomsNumbersService $importCustomsNumbersService)
    {
        $this->logger = $login;
        $this->importCustomsNumbersService = $importCustomsNumbersService;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Imports global customs numbers from csv to catalog ...' );
        $this->addArgument(self::FILE, InputArgument::REQUIRED, 'Iš kokio failo importuoti kodus' );
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

        $file = $input->getArgument(self::FILE);
        $count = $this->importCustomsNumbersService->importFromCsv($file);

        $this->logger->debug('Imported '.$count. ' customs numbers objects' );

        if ( $count > 0 ) {
            return 0;
        }
        else {
            return -1;
        }
    }


}