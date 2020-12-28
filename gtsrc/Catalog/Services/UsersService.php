<?php
/**
 * UsersService.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-28
 * Time: 13:58
 */

namespace Gt\Catalog\Services;


use Psr\Log\LoggerInterface;

class UsersService
{
    /** @var LoggerInterface */
    protected $logger;

    /**
     * UsersService constructor.
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }


}