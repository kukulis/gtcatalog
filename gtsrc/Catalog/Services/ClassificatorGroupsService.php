<?php


namespace Gt\Catalog\Services;


use Gt\Catalog\Dao\ClassificatorGroupDao;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Form;

class ClassificatorGroupsService
{
    const PAGE_SIZE = 10;

    /** @var LoggerInterface */
    private $logger;

    /** @var ClassificatorGroupDao */
    private $classificatorGroupDao;

    /**
     * ClassificatorGroupsService constructor.
     * @param LoggerInterface $logger
     * @param ClassificatorGroupDao $classificatorGroupDao
     */
    public function __construct(LoggerInterface $logger, ClassificatorGroupDao $classificatorGroupDao)
    {
        $this->logger = $logger;
        $this->classificatorGroupDao = $classificatorGroupDao;
    }

    public function newClassificatorGroup(Form $form)
    {
        $data = $form->getData();
        $this->classificatorGroupDao->addClassificatorGroup($data);
    }

    public function getClassificatorGroups($page = 0)
    {
        return $this->classificatorGroupDao->getClassificatorGroups($page * self::PAGE_SIZE, self::PAGE_SIZE);
    }

}