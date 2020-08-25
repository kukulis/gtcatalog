<?php


namespace Gt\Catalog\Dao;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\ClassificatorLanguage;
use Psr\Log\LoggerInterface;

class ClassificatorDao
{
    /** @var LoggerInterface */
    private $logger;

    /** @var Registry */
    private $doctrine;

    /**
     * ClassificatorDao constructor.
     * @param LoggerInterface $logger
     * @param Registry $doctrine
     */
    public function __construct(LoggerInterface $logger, Registry $doctrine)
    {
        $this->logger = $logger;
        $this->doctrine = $doctrine;
    }

    /**
     * @param Classificator $data
     */
    public function storeClassificator(Classificator $data)
    {
        $em = $this->doctrine->getManager();
        $em->persist($data);
        $em->flush();
    }

    /**
     * @param ClassificatorLanguage $cl
     */
    public function storeClassificatorLanguage(ClassificatorLanguage $cl ) {
        $em = $this->doctrine->getManager();
        $em->persist($cl);
        $em->flush();
    }

    /**
     * @param string $code
     * @return Classificator
     */
    public function loadClassificator ( $code ) {
        $em = $this->doctrine->getManager();
        /** @var Classificator $classificator */
        $classificator = $em->find(Classificator::class, $code);
        return $classificator;
    }

    /**
     * @param $code
     * @param $languageCode
     * @return ClassificatorLanguage
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function loadClassificatorLanguage ( $code, $languageCode ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $class = ClassificatorLanguage::class;
        $dql = /** @lang DQL */ "SELECT cl, c, l from $class cl JOIN cl.classificator c JOIN cl.language l WHERE c.code=:code and l.code = :languageCode";
        $query = $em->createQuery($dql);

        $query->setParameter('code', $code );
        $query->setParameter('languageCode', $languageCode );

        /** @var ClassificatorLanguage $rez */
        $rez = $query->getOneOrNullResult();
        return $rez;
    }

    /**
     * @param $codes
     * @param $languageCode
     * @return ClassificatorLanguage[]
     */
    public function loadClassificatorsLanguages ( $codes, $languageCode ) {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();
        $class = ClassificatorLanguage::class;
        $dql = /** @lang DQL */ "SELECT cl FROM $class cl JOIN cl.classificator c JOIN cl.language l WHERE c.code in (:codes) and l.code = :languageCode";
        $query = $em->createQuery($dql);
        $query->setParameter('codes', $codes );
        $query->setParameter('languageCode', $languageCode );

        /** @var ClassificatorLanguage[] $classificatorLanguages */
        $classificatorLanguages = $query->getResult();

        return $classificatorLanguages;
    }
}