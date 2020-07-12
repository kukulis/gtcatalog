<?php


namespace Gt\Catalog\Services;


use Gt\Catalog\Dao\LanguageDao;
use Psr\Log\LoggerInterface;

class LanguagesService
{
    const PAGE_SIZE = 10;

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

    public function getLanguages( $page=0)
    {
        return $this->languageDao->getLanguagesList($page * self::PAGE_SIZE, self::PAGE_SIZE);
    }

}