<?php
/**
 * AutoAssignCustomsNumbersByKeywordsService.php
 * Created by Giedrius Tumelis.
 * Date: 2021-04-08
 * Time: 08:43
 */

namespace Gt\Catalog\Services;

use Psr\Log\LoggerInterface;

class AutoAssignCustomsNumbersByKeywordsService
{
    /** @var LoggerInterface */
    private $logger;

//    /** @var Registry */
//    private $doctrine;

    /** @var CustomsKeywordsService */
    private $customsKeywordsService;

    /** @var string[]  */
    private $languages=[];

    /**
     * AutoAssignCustomsNumbersByKeywordsService constructor.
     * @param LoggerInterface $logger
     * @param CustomsKeywordsService $customsKeywordsService
     */
    public function __construct(LoggerInterface $logger, CustomsKeywordsService $customsKeywordsService)
    {
        $this->logger = $logger;
        $this->customsKeywordsService = $customsKeywordsService;
    }

    /**
     * @return int
     * @throws \Doctrine\DBAL\Exception
     */
    public function autoAssign() {
        $this->customsKeywordsService->calculateLikeKeywords();
        return $this->customsKeywordsService->assignCustomCodesByKeywords($this->languages);
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

    /**
     * @return array
     * @throws \Doctrine\DBAL\Exception
     */
    public function showUpdates($max) {
        $this->customsKeywordsService->calculateLikeKeywords();
        return $this->customsKeywordsService->showAssignementPrognoseByKeywords($this->languages, $max);
    }
}