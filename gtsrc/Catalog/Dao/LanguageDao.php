<?php


namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Gt\Catalog\Entity\Language;
use Psr\Log\LoggerInterface;

/**
 * @todo perdaryti su Doctrina ir pajungti Symfony formas
 *
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

    public function getLanguagesList($offset, $limit)
    {
        $languageClass = Language::class;
        $dql = /** @lang DQL */ "SELECT l from $languageClass l";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $languages = $em->createQuery($dql)->setMaxResults($limit)->setFirstResult($offset)->execute();
        return $languages;
    }

    public function addLanguage($code, $name, $locale_code)
    {
        $languageClass = Language::class;
        $dql = /** @lang DQL */ "INSERT INTO $languageClass (code, name, locale_code) 
                                     VALUES ($code, $name, $locale_code)";

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $em->createQuery($dql)->execute();
    }
}