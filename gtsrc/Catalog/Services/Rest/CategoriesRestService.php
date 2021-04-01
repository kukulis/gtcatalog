<?php
/**
 * CategoriesRestService.php
 * Created by Giedrius Tumelis.
 * Date: 2021-03-30
 * Time: 16:12
 */

namespace Gt\Catalog\Services\Rest;


use Gt\Catalog\Dao\LanguageDao;
use Gt\Catalog\Data\SimpleCategoriesFilter;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Services\CategoriesService;
use Gt\Catalog\Utils\CategoriesMapper;
use Psr\Log\LoggerInterface;

class CategoriesRestService
{
    /** @var LoggerInterface */
    private $logger;

    /** @var CategoriesService */
    private $categoriesService;

    /** @var LanguageDao */
    private $languageDao;

    /**
     * CategoriesRestService constructor.
     * @param LoggerInterface $logger
     * @param CategoriesService $categoriesService
     * @param LanguageDao $languageDao
     */
    public function __construct(LoggerInterface $logger, CategoriesService $categoriesService, LanguageDao $languageDao)
    {
        $this->logger = $logger;
        $this->categoriesService = $categoriesService;
        $this->languageDao = $languageDao;
    }


    public function getRestCategories($lang) {
        $categoriesFilter = new SimpleCategoriesFilter();
        $language = $this->languageDao->getLanguage($lang);
        if ( $language == null ) {
            throw new CatalogValidateException('Wrong language code '.$lang );
        }
        $categoriesFilter->setLanguage($language);
        $categories = $this->categoriesService->getCategoriesLanguages($categoriesFilter);
        $restCategories = array_map ( [CategoriesMapper::class, 'mapToRestCategory'], $categories);
        return $restCategories;
    }

    public function getCategoriesRoots() {
        // TODO
    }

    public function getCategoriesTree($categoryCode, $lang ) {
        // TODO
    }


}