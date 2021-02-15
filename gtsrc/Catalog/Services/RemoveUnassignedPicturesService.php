<?php
/**
 * RemoveUnassignedPicturesService.php
 * Created by Giedrius Tumelis.
 * Date: 2021-02-15
 * Time: 11:28
 */

namespace Gt\Catalog\Services;


use Psr\Log\LoggerInterface;

class RemoveUnassignedPicturesService
{
    const STEP = 500;

    /** @var LoggerInterface */
    private $logger;

    /** @var PicturesService */
    private $picturesService;

    /**
     * RemoveUnassignedPicturesService constructor.
     * @param LoggerInterface $logger
     * @param PicturesService $picturesService
     */
    public function __construct(LoggerInterface $logger, PicturesService $picturesService)
    {
        $this->logger = $logger;
        $this->picturesService = $picturesService;
    }

    public function findAndRemoveUnassignedPictures($remove = true, $messagesCount=100 ) {
        $fromId = 0;

        $somePictures = ['-'];
        $count = 0;
        while (count($somePictures) > 0) {
            $somePictures = $this->picturesService->getSomePictures($fromId, self::STEP);

            $lastId = 0;
            foreach ($somePictures as $picture) {
                $lastId = $picture->getId();
                $pps = $this->picturesService->findPictureAssignements($picture->getId());
                if (count($pps) == 0) {
                    $this->logger->notice('Picture with id ' . $picture->getId() . ' is not assigned to any product');
                    $path = $this->picturesService->calculatePicturePath($picture->getId(), $picture->getName());
                    if ( $remove ) {
                        if ( $messagesCount > 0 ) {
                            $this->logger->notice('Removing picture from path '.$path);
                            $messagesCount--;
                        }
                        unlink($path);
                    }
                    $count++;
                }
            }
            $this->logger->debug('From ID '.$fromId.' found '.$count.' unassigned pictures ');
            $fromId = $lastId;
        }

        return $count;
    }
}