<?php
/**
 * ImportPicturesJob.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-20
 * Time: 16:22
 */

namespace Gt\Catalog\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gt\Catalog\Repository\ImportPicturesJobRepository;
use \DateTime;

/**
 * @ORM\Entity(repositoryClass=ImportPicturesJobRepository::class)
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
     * @var string
     * @ORM\Column(type="string", length=255, name="name", nullable=true)
     */
    private $name;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", name="created_time")
     *
     */
    private $createdTime;

    /**
     * @var DateTime
     * @ORM\Column(type="datetime", name="finished_time", nullable=true)
     *
     */
    private $finishedTime;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, name="status")
     */
    private $status=self::STATUS_NONE;

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
     * @ORM\Column(type="string", length=255, name="original_zip_file", nullable=true)
     */
    private $originalZipFile;


    /**
     * @var string
     * @ORM\Column(type="string", length=255, name="original_csv_file", nullable=true)
     */
    private $originalCsvFile;


    /**
     * @var string
     * @ORM\Column(type="text", name="message", nullable=true)
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
     * @return DateTime
     */
    public function getCreatedTime(): DateTime
    {
        return $this->createdTime;
    }

    /**
     * @param DateTime $createdTime
     */
    public function setCreatedTime(DateTime $createdTime): void
    {
        $this->createdTime = $createdTime;
    }

    /**
     * @return DateTime
     */
    public function getFinishedTime(): DateTime
    {
        return $this->finishedTime;
    }

    /**
     * @param DateTime $finishedTime
     */
    public function setFinishedTime(DateTime $finishedTime): void
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

    /**
     * @return string
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getOriginalZipFile(): string
    {
        return $this->originalZipFile;
    }

    /**
     * @param string $originalZipFile
     */
    public function setOriginalZipFile(string $originalZipFile): void
    {
        $this->originalZipFile = $originalZipFile;
    }

    /**
     * @return string
     */
    public function getOriginalCsvFile(): string
    {
        return $this->originalCsvFile;
    }

    /**
     * @param string $originalCsvFile
     */
    public function setOriginalCsvFile(string $originalCsvFile): void
    {
        $this->originalCsvFile = $originalCsvFile;
    }
}