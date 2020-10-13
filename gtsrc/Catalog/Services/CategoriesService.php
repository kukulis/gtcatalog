<?php


namespace Gt\Catalog\Services;


use Gt\Catalog\Dao\CategoryDao;
use Gt\Catalog\Dao\LanguageDao;
use Gt\Catalog\Data\CategoriesFilter;
use Gt\Catalog\Entity\Category;
use Gt\Catalog\Entity\CategoryLanguage;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\FormInterface;

class CategoriesService
{
    const PAGE_SIZE = 10;

    const DEFAULT_LANGUAGE_CODE = 'en';

    /** @var LoggerInterface */
    private $logger;
    /** @var CategoryDao */
    private $categoryDao;

    /** @var LanguageDao */
    private $languageDao;

    /**
     * CategoriesService constructor.
     * @param LoggerInterface $logger
     * @param CategoryDao $categoryDao
     */
    public function __construct(LoggerInterface $logger,
                                CategoryDao $categoryDao,
                                LanguageDao $languageDao )
    {
        $this->logger = $logger;
        $this->categoryDao = $categoryDao;
        $this->languageDao = $languageDao;
    }

    public function newCategory(FormInterface $form)
    {
        $data = $form->getData();
        $this->categoryDao->addCategory($data);
    }

    /**
     * @param CategoriesFilter $filter
     * @return CategoryLanguage[]
     * @throws \Gt\Catalog\Exception\CatalogErrorException
     */
    public function getCategoriesLanguages(CategoriesFilter  $filter)
    {
         $categories = $this->categoryDao->getCategories($filter);
         $codes = array_map ([Category::class, 'lambdaGetCode'], $categories );

         $languageCode = self::DEFAULT_LANGUAGE_CODE;
         if ( $filter->getLanguage() != null ) {
             $languageCode = $filter->getLanguage()->getCode();
         }

         $categoriesLanguages = $this->categoryDao->getCategoriesLanguages($codes, $languageCode);

         /** @var CategoryLanguage[] $clMap */
         $clMap = [];

         foreach ($categoriesLanguages as $cl ) {
             $clMap[$cl->getCategory()->getCode()] = $cl;
         }

         /** @var CategoryLanguage[] $clResult */
         $clResult = [];

         $language = $this->languageDao->getLanguage($languageCode);
         foreach ($categories as $c ) {
             if ( array_key_exists($c->getCode(), $clMap)) {
                 $cl = $clMap[$c->getCode()];
             }
             else {
                 $cl = new CategoryLanguage();
                 $cl->setCategory($c);
                 $cl->setLanguage($language);
             }
             $clResult[] = $cl;
         }
         return $clResult;
    }

}