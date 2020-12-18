<?php
/**
 * CustomsNumberDao.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-18
 * Time: 13:43
 */

namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityManager;
use Gt\Catalog\Entity\CustomsNumber;
use Psr\Log\LoggerInterface;

class CustomsNumberDao
{
    /** @var LoggerInterface */
    private $logger;

    /** @var Registry */
    private $doctrine;

    /**
     * CustomsNumberDao constructor.
     * @param LoggerInterface $logger
     * @param Registry $doctrine
     */
    public function __construct(LoggerInterface $logger, Registry $doctrine)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    /**
     * @param CustomsNumber[] $customNumbers
     * @return int
     * @throws DBALException
     */
    public function importCustomNumers( $customNumbers ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $conn = $em->getConnection();

        $lines = [];
        foreach ($customNumbers as $customNumber ) {
            $line = [
                $customNumber->getSortingCode(),
                $customNumber->getOfficialCode(),
                $customNumber->getDescription(),
            ];

            $qLine = array_map ( [$conn, 'quote'], $line );
            $lineStr = '('.join(',', $qLine ).')';

            $lines[] = $lineStr;
        }

        $valuesStr = join( ",\n", $lines );

        $sql = /** @lang MySQL */ "
            INSERT INTO customs_numbers ( sorting_code, official_code, description)
            VALUES $valuesStr
            ON DUPLICATE KEY 
                UPDATE sorting_code=values(sorting_code),
                       official_code=values(official_code),
                       description=values(description)";

        return $conn->exec($sql);
    }
}