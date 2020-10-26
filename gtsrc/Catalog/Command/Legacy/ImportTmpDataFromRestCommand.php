<?php
/**
 * ImportTmpDataFromRestCommand.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-21
 * Time: 15:08
 */

namespace Gt\Catalog\Command\Legacy;


use Gt\Catalog\Services\Legacy\LegacyImporterService;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
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
    const URL='url';
    const LOCALE='locale';

    /** @var LoggerInterface */
    private $logger;

    /** @var LegacyImporterService */
    private $legacyImporterService;

    /**
     * ImportTmpDataFromRestCommand constructor.
     * @param LoggerInterface $logger
     * @param LegacyImporterService $legacyImporterService
     */
    public function __construct(LoggerInterface $logger, LegacyImporterService $legacyImporterService)
    {
        $this->logger = $logger;
        $this->legacyImporterService = $legacyImporterService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Imports tmp data from the legacy rest' );

        $this->addArgument(self::URL, InputArgument::REQUIRED, 'iš kokio url importuoti duomenis' );
        $this->addArgument(self::LOCALE, InputArgument::REQUIRED, 'Kokia lokalė' );
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



        try {
            $url = $input->getArgument(self::URL);
            $locale = $input->getArgument(self::LOCALE);
            $count = $this->legacyImporterService->importToTmp($url, $locale);
            $this->logger->debug('Result count=' . $count);
        } catch ( \Error | \ErrorException $e ) {

            $output->writeln('ERROR:'.$e->getMessage());
            $output->writeln( $e->getTraceAsString()) ;
            return 1;
        }
        return 0;
    }
}