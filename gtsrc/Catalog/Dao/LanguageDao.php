<?php


namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Gt\Catalog\Entity\Language;
use Gt\Catalog\Exception\CatalogErrorException;
use Psr\Log\LoggerInterface;

/**
 * Class LanguageDao
 * @package Gt\Catalog\Dao
 */
class LanguageDao
{

    /** @var LoggerInterface */
    private $logger;

    /** @var Registry */
    private $doctrine;

    /**
     * LanguageDao constructor.
     * @param LoggerInterface $logger
     * @param Registry $doctrine
     */
    public function __construct(LoggerInterface $logger, Registry $doctrine)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    /**
     * @param int $offset
     * @param int $limit
     * @return Language[]
     */
    public function getLanguagesList($offset, $limit)
    {
        $languageClass = Language::class;
        $dql = /** @lang DQL */ "SELECT l FROM $languageClass l";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        /** @var Language[] $languages */
        $languages = $em->createQuery($dql)->setMaxResults($limit)->setFirstResult($offset)->execute();
        return $languages;
    }

    /**
     * @param $data
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function addLanguage($data)
    {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $em->persist($data);
        $em->flush();
    }

    /**
     * @param $languageCode
     * @return Language
     * @throws CatalogErrorException
     */
    public function getLanguage ( $languageCode ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        try {
            /** @var Language $language */
            $language = $em->find(Language::class, $languageCode);

            return $language;
        } catch ( ORMException $e ) {
            throw new CatalogErrorException( $e->getMessage());
        }
    }
}