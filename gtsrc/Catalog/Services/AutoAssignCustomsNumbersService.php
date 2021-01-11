<?php
/**
 * AutoAssignCustomsNumbersService.php
 * Created by Giedrius Tumelis.
 * Date: 2021-01-11
 * Time: 09:47
 */

namespace Gt\Catalog\Services;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

class AutoAssignCustomsNumbersService
{

    /** @var LoggerInterface */
    private $logger;

    /** @var Registry */
    private $doctrine;

    /**
     * AutoAssignCustomsNumbersService constructor.
     * @param LoggerInterface $logger
     * @param Registry $doctrine
     */
    public function __construct(LoggerInterface $logger, Registry $doctrine)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    /**
     * @return int
     * @throws \Doctrine\DBAL\DBALException
     */
    public function autoAssignCustomsNumbers() {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $conn = $em->getConnection();

        $sqlFromCategories = /** @lang MySQL */
        "update products p join products_categories pc on p.sku = pc.sku
join categories c on pc.category = c.code
set p.code_from_custom = c.customs_code
where c.customs_code is not null and ( p.code_from_custom is null or p.code_from_custom='')";

        $countFromCategories  = $conn->exec($sqlFromCategories);
        $this->logger->debug('Updated count from categories: '.$countFromCategories );

        $sqlFromTypes = /** @lang MySQL */
        "update products p join classificators c on p.type = c.code
set p.code_from_custom = c.customs_code
where c.customs_code is not null and ( p.code_from_custom is null or p.code_from_custom = '' )";

        $countFromTypes  = $conn->exec($sqlFromTypes);
        $this->logger->debug('Updated count from types: '.$countFromTypes );

        return $countFromTypes+$countFromCategories;
    }
}