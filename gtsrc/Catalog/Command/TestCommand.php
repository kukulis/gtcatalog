<?php
/**
 * TestCommand.php
 * Created by Giedrius Tumelis.
 * Date: 2021-01-11
 * Time: 11:32
 */

namespace Gt\Catalog\Command;


use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class TestCommand extends Command
{

    /** @var LoggerInterface */
    private $logger;

    /**
     * TestCommand constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;

        parent::__construct();
    }


    protected function configure()
    {
        $this->setDescription('Testing symfony logging' );
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->logger->error('This is a testing error' );
        return 0;
    }
}