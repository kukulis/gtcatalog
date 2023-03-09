<?php

namespace Gt\Catalog\Dao;

use Doctrine\ORM\EntityManager;
use Gt\Catalog\Data\TmpPackage;
use JMS\Serializer\Serializer;

class TmpPackagesDao extends BaseDao
{
    private EntityManager $entityManager;
    private Serializer $serializer;

    public function __construct(EntityManager $entityManager, Serializer $serializer)
    {
        $this->entityManager = $entityManager;
        $this->serializer = $serializer;
    }

    /**
     * @return TmpPackage[]
     */
    public function fetchAll($fromNomnr, $limit = 500): array
    {
        $connection = $this->entityManager->getConnection();
        $qNomnr = $connection->quote($fromNomnr);
        $nomnrCondition = '';
        if ($fromNomnr) {
            $nomnrCondition = "AND nomnr > $qNomnr";
        }
        $sql = /** @lang MySQL */
            "SELECT * from tmp_pakuotes where 1 = 1 $nomnrCondition LIMIT $limit";
        $associative = $connection->executeQuery($sql)->fetchAllAssociative();
        $result = $this->serializer->fromArray($associative, sprintf('array<%s>', TmpPackage::class));

        return $result;
    }
}