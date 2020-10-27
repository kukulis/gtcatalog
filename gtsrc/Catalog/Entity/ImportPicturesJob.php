<?php
/**
 * ImportPicturesJob.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-20
 * Time: 16:22
 */

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;


/**
 * @ORM\Entity
 * @ORM\Table(name="import_pictures_job")
 */
class ImportPicturesJob
{
    const STATUS_NONE = 'none';
    const STATUS_CREATED = 'created';
    const STATUS_IN_QUEUE = 'in_queue';
    const STATUS_PROCESSING = 'processing';
    const STATUS_FINISHED = 'finished';
    const STATUS_FAIL = 'fail';
    const STATUS_CANCELED = 'canceled';

    /**
     * @var int
     * @ORM\Id
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="created_time")
     *
     */
    private $createdTime;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", name="finished_time")
     *
     */
    private $finishedTime;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, name="status")
     */
    private $status=self::STATUS_NONE;

// nereikia, nes darysim pagal id
//    private $workDir;
//    private $zipFilePath; // in the same dir always same file
//    private $csvFilePath; // in the same dir always same file

    // statistics
    /**
     * @var int
     * @ORM\Column(type="integer", name="total_pictures", nullable=true)
     */
    private $totalPictures;

    /**
     * @var int
     * @ORM\Column(type="integer", name="imported_pictures", nullable=true)
     */
    private $importedPictures;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, name="last_sku", nullable=true)
     */
    private $lastSku;


    /**
     * @var string
     * @ORM\Column(type="string", length=255, name="message", nullable=true)
     */
    private $message;


    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    /**
     * @return int
     */
    public function getTotalPictures(): int
    {
        return $this->totalPictures;
    }

    /**
     * @param int $totalPictures
     */
    public function setTotalPictures(int $totalPictures): void
    {
        $this->totalPictures = $totalPictures;
    }

    /**
     * @return int
     */
    public function getImportedPictures(): int
    {
        return $this->importedPictures;
    }

    /**
     * @param int $importedPictures
     */
    public function setImportedPictures(int $importedPictures): void
    {
        $this->importedPictures = $importedPictures;
    }

    /**
     * @return string
     */
    public function getLastSku(): string
    {
        return $this->lastSku;
    }

    /**
     * @param string $last_sku
     */
    public function setLastSku(string $last_sku): void
    {
        $this->lastSku = $last_sku;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedTime(): \DateTime
    {
        return $this->createdTime;
    }

    /**
     * @param \DateTime $createdTime
     */
    public function setCreatedTime(\DateTime $createdTime): void
    {
        $this->createdTime = $createdTime;
    }

    /**
     * @return \DateTime
     */
    public function getFinishedTime(): \DateTime
    {
        return $this->finishedTime;
    }

    /**
     * @param \DateTime $finishedTime
     */
    public function setFinishedTime(\DateTime $finishedTime): void
    {
        $this->finishedTime = $finishedTime;
    }

    /**
     * @return string
     */
    public function getMessage(): string
    {
        return $this->message;
    }

    /**
     * @param string $message
     */
    public function setMessage(string $message): void
    {
        $this->message = $message;
    }
}