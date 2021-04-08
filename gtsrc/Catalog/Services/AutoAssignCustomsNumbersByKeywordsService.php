<?php
/**
 * AutoAssignCustomsNumbersByKeywordsService.php
 * Created by Giedrius Tumelis.
 * Date: 2021-04-08
 * Time: 08:43
 */

namespace Gt\Catalog\Services;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use Psr\Log\LoggerInterface;

class AutoAssignCustomsNumbersByKeywordsService
{
    /** @var LoggerInterface */
    private $logger;

    /** @var Registry */
    private $doctrine;

    /** @var string[]  */
    private $languages=[];

    /**
     * AutoAssignCustomsNumbersByKeywordsService constructor.
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
     * @throws \Doctrine\DBAL\Exception
     */
    public function calculateLikeKeywords() {
        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $conn = $em->getConnection();

        $sql = /** @lang MySQL */ "update customs_keywords set like_keyword = concat('%', keyword, '%')";
        return $conn->executeStatement($sql);
    }

    /**
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function autoAssign() {
        $this->logger->debug('autoAssign called' );

        /** @var EntityManager $em */
        $em = $this->doctrine->getManager();

        $this->calculateLikeKeywords();

        $conn = $em->getConnection();
        $qLanguages = array_map([$conn, 'quote'], $this->languages);
        $languagesStr = join ( ',', $qLanguages );

        $sql = /** @lang MySQL */
        "update
            products p join
            products_languages pl ON p.sku = pl.sku
           JOIN customs_keywords ck
           ON pl.name like like_keyword
        SET p.code_from_custom = ck.customs_code
        where language in ($languagesStr) and ( p.code_from_custom is null || p.code_from_custom=''  )";

        return $conn->executeStatement($sql);
    }

    /**
     * @return string[]
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * @param string[] $languages
     */
    public function setLanguages(array $languages): void
    {
        $this->languages = $languages;
    }
}