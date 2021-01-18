<?php
/**
 * HealthSearchUnexistingPictures.php
 * Created by Giedrius Tumelis.
 * Date: 2021-01-18
 * Time: 09:13
 */

namespace Gt\Catalog\Command;


use Gt\Catalog\Services\PicturesService;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class HealthSearchUnexistingPicturesCommand extends Command
{
    const ACTION = 'action';

    const ACTION_SHOW = 'show';
    const ACTION_DELETE = 'delete';

    const ACTIONS_ARR = [self::ACTION_SHOW, self::ACTION_DELETE  ];


    /** @var LoggerInterface */
    private $logger;

    /** @var PicturesService */
    private $picturesService;

    /**
     * HealthSearchUnexistingPicturesCommand constructor.
     * @param LoggerInterface $logger
     * @param PicturesService $picturesService
     */
    public function __construct(LoggerInterface $logger, PicturesService $picturesService)
    {
        $this->logger = $logger;
        $this->picturesService = $picturesService;

        parent::__construct();
    }

    protected function configure()
    {
        $this->setDescription('Imports global customs numbers from csv to catalog ...' );

        $actionsStr = '('. join ( ", ", self::ACTIONS_ARR ) . ')';
        $this->addArgument(self::ACTION, InputArgument::REQUIRED, 'What to do with a found picture : '.$actionsStr );
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

        $action = $input->getArgument(self::ACTION );
        if ( array_search($action, self::ACTIONS_ARR) === false ) {
            $actionsStr = '('. join (', ', self::ACTIONS_ARR) . ')';
            $this->logger->error('Wrong action '.$action.'. Possible values are '. $actionsStr );
            return -1;
        }

        $count = $this->picturesService->searchUnexistingPictures($action);

        $this->logger->info('found '.$count. ' unexisting pictures' );
        return 0;
    }
}