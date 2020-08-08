<?php


namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Gt\Catalog\Entity\ClassificatorGroup;
use Psr\Log\LoggerInterface;

class ClassificatorGroupDao
{
    /** @var LoggerInterface */
    private $logger;

    /** @var Registry */
    private $doctrine;

    /**
     * ClassificatorGroupDao constructor.
     * @param LoggerInterface $logger
     * @param Registry $doctrine
     */
    public function __construct(LoggerInterface $logger, Registry $doctrine)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    public function addClassificatorGroup($data)
    {
        $em = $this->doctrine->getManager();
        $em->persist($data);
        $em->flush();
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return ClassificatorGroup[]
     */
    public function getClassificatorGroups($offset, $limit)
    {
        $classificatorGroupClass = ClassificatorGroup::class;
        $dql =  /** @lang DQL */ "SELECT cg FROM $classificatorGroupClass cg";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        /** @var ClassificatorGroup[] $classifictorGroups */
        $classifictorGroups = $em->createQuery($dql)->setMaxResults($limit)->setFirstResult($offset)->execute();
        return $classifictorGroups;
    }

}