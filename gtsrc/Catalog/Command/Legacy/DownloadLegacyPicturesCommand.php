<?php
/**
 * DownloadLegacyPicturesCommand.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-27
 * Time: 12:12
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

class DownloadLegacyPicturesCommand  extends Command
{
    const URL = 'url';

    /** @var LoggerInterface */
    private $logger;

    /** @var LegacyImporterService */
    private $legacyImporterService;

    /**
     * DownloadLegacyPicturesCommand constructor.
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
        $this->setDescription('Downloads pictures by the local tmp_products_images table' );
        $this->addArgument(self::URL, InputArgument::REQUIRED, 'iš kokio url importuoti duomenis' );
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
            $count = $this->legacyImporterService->downloadPictures($url);
            $this->logger->debug('Result count=' . $count);
        } catch ( \Error | \ErrorException $e ) {

            $output->writeln('ERROR:'.$e->getMessage());
            $output->writeln( $e->getTraceAsString()) ;
            return 1;
        }
        return 0;
    }
}