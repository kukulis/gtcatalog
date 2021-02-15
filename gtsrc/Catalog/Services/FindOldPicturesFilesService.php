<?php
/**
 * FindOldPicturesFilesService.php
 * Created by Giedrius Tumelis.
 * Date: 2021-02-15
 * Time: 13:23
 */

namespace Gt\Catalog\Services;


use Psr\Log\LoggerInterface;

class FindOldPicturesFilesService
{
    /** @var LoggerInterface */
    private $logger;

    /** @var PicturesService */
    private $picturesService;

    /**
     * FindOldPicturesFilesService constructor.
     * @param LoggerInterface $logger
     * @param PicturesService $picturesService
     */
    public function __construct(LoggerInterface $logger, PicturesService $picturesService)
    {
        $this->logger = $logger;
        $this->picturesService = $picturesService;
    }

    public function findFiles($remove) {
        // TODO scan files

        // TODO use directory scanner
    }

}