<?php


namespace Gt\Catalog\Services;


use Gt\Catalog\Dao\CategoryDao;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Form;

class CategoriesService
{
    const PAGE_SIZE = 10;

    /** @var LoggerInterface */
    private $logger;
    /** @var CategoryDao */
    private $categoryDao;

    /**
     * CategoriesService constructor.
     * @param LoggerInterface $logger
     * @param CategoryDao $categoryDao
     */
    public function __construct(LoggerInterface $logger, CategoryDao $categoryDao)
    {
        $this->logger = $logger;
        $this->categoryDao = $categoryDao;
    }

    public function newCategory(Form $form)
    {
        $data = $form->getData();
        $this->categoryDao->addCategory($data);
    }

    public function getCategories($page = 0)
    {
        return $this->categoryDao->getCategories($page * self::PAGE_SIZE, self::PAGE_SIZE);
    }

}