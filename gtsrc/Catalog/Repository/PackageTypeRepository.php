<?php

namespace Gt\Catalog\Repository;


use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Gt\Catalog\Entity\PackageType;

class PackageTypeRepository  extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PackageType::class);
    }
}