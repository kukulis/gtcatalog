<?php
/**
 * CustomsKeywordsRepository.php
 * Created by Giedrius Tumelis.
 * Date: 2021-04-07
 * Time: 12:50
 */

namespace Gt\Catalog\Repository;


use Doctrine\DBAL\DBALException;
use Doctrine\ORM\EntityRepository;
use Gt\Catalog\Data\CustomsKeywordsFilter;
use Gt\Catalog\Entity\CustomsKeyword;

class CustomsKeywordsRepository extends EntityRepository
{
    /**
     * @param CustomsKeywordsFilter $filter
     * @return CustomsKeyword[]
     */
    public function getKeywords(CustomsKeywordsFilter $filter) {
        $class = CustomsKeyword::class;
        $dql = /** @lang DQL */ "SELECT k FROM $class k ORDER BY k.customsCode";

        $query = $this->_em->createQuery($dql);
        $query->setFirstResult($filter->getOffset());
        $query->setMaxResults($filter->getLimit());

        /** @var CustomsKeyword[] $customsKeywords */
        $customsKeywords = $query->getResult();

        return $customsKeywords;
    }

    /**
     * @param CustomsKeyword[] $keywords
     * @return int
     * @throws DBALException
     */
    public function importKeywords($keywords) {
        $conn = $this->_em->getConnection();
        $values = [];

        foreach ($keywords as $keyword) {
            $line = [
                $conn->quote( $keyword->getCustomsCode()),
                $conn->quote( $keyword->getKeyword()),
            ];
            $lineStr = '('.join(',', $line).')';
            $values[] = $lineStr;
        }

        $valuesStr = join(",\n", $values );
        $sql = /** @lang MySQL */
        "INSERT IGNORE INTO customs_keywords ( customs_code, keyword )
        values $valuesStr";
        return $conn->executeStatement($sql);
    }
}