<?php


namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Gt\Catalog\Entity\Category;
use Psr\Log\LoggerInterface;

class CategoryDao
{
    private LoggerInterface $logger;
    private Registry $doctrine;

    /**
     * CategoryDao constructor.
     * @param LoggerInterface $logger
     * @param Registry $doctrine
     */
    public function __construct(LoggerInterface $logger, Registry $doctrine)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    /**
     * @param $data
     */
    public function addCategory($data)
    {
        $em = $this->doctrine->getManager();
        $em->persist($data);
        $em->flush();
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return int|mixed|string
     */
    public function getCategories($offset, $limit)
    {
        $categoryClass = Category::class;
        $dql = "SELECT c FROM $categoryClass c";
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        return $em->createQuery($dql)->setMaxResults($limit)->setFirstResult($offset)->execute();
    }


}