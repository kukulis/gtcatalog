<?php
/**
 * PicturesDao.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-12
 * Time: 10:38
 */

namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Gt\Catalog\Entity\Picture;
use Psr\Log\LoggerInterface;

class PicturesDao
{
    /** @var LoggerInterface */
    private $logger;

    /** @var Registry */
    private $doctrine;

    /**
     * PicturesDao constructor.
     * @param LoggerInterface $logger
     * @param Registry $doctrine
     */
    public function __construct(LoggerInterface $logger, Registry $doctrine)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    public function insertPicture (Picture $p) {
        $em = $this->doctrine->getManager();
        $em->persist($p);
        $em->flush();
        return $p;
    }


}