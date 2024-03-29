<?php


namespace Gt\Catalog\Services;


use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Gt\Catalog\Dao\LanguageDao;
use Psr\Log\LoggerInterface;
use Symfony\Component\Form\Form;

class LanguagesService
{
    const PAGE_SIZE = 100;

    /** @var LoggerInterface */
    private $logger;

    /** @var LanguageDao */
    private $languageDao;

    /**
     * LanguagesService constructor.
     * @param LoggerInterface $logger
     * @param LanguageDao $languageDao
     */
    public function __construct(LoggerInterface $logger, LanguageDao $languageDao)
    {
        $this->logger = $logger;
        $this->languageDao = $languageDao;
    }

    /**
     * @param int $page
     * @return \Gt\Catalog\Entity\Language[]
     */
    public function getLanguages( $page=0)
    {
        return $this->languageDao->getLanguagesList($page * self::PAGE_SIZE, self::PAGE_SIZE);
    }

    public function newLanguage(Form $form)
    {
        $data = $form->getData();
        try {
            $this->languageDao->addLanguage($data);
        } catch (OptimisticLockException $e) {
        } catch (ORMException $e) {
        }
    }

}