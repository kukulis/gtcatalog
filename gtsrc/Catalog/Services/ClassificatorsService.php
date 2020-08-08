<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.8.8
 * Time: 07.31
 */

namespace Gt\Catalog\Services;


use Gt\Catalog\Dao\CatalogDao;
use Gt\Catalog\Dao\ClassificatorGroupDao;
use Gt\Catalog\Data\ClassificatorsListFilter;
use Gt\Catalog\Entity\ClassificatorGroup;
use Psr\Log\LoggerInterface;

class ClassificatorsService
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ClassificatorGroupDao */
    private $classificatorGroupDao;

    /** @var CatalogDao */
    private $catalogDao;

    /**
     * ClassificatorsService constructor.
     * @param LoggerInterface $logger
     * @param ClassificatorGroupDao $classificatorGroupDao
     */
    public function __construct(LoggerInterface $logger, ClassificatorGroupDao $classificatorGroupDao, CatalogDao $catalogDao)
    {
        $this->logger = $logger;
        $this->classificatorGroupDao = $classificatorGroupDao;
        $this->catalogDao = $catalogDao;
    }

    /**
     * @return ClassificatorGroup[]
     */
    public function getAllGroups() {
        return $this->classificatorGroupDao->getClassificatorGroups(0, 50 );
    }

    /**
     * @param ClassificatorsListFilter $filter
     * @return \Gt\Catalog\Entity\Classificator[]
     */
    public function searchClassificators (ClassificatorsListFilter $filter) {
        return $this->catalogDao->loadLikeClassificators($filter->getLikeCode(), $filter->getLikeName(), $filter->getGroupCode(), $filter->getLimit());
    }
}