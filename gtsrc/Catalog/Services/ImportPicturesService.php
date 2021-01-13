<?php
/**
 * ImportPicturesService.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-30
 * Time: 15:11
 */

namespace Gt\Catalog\Services;


use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Data\IPicturesJobsFilter;
use Gt\Catalog\Entity\ImportPicturesJob;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Repository\ImportPicturesJobRepository;
use Gt\Catalog\Utils\ProductsHelper;
use Gt\Catalog\Utils\ValidateHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use \DateTime;

class ImportPicturesService
{
    const PICTURES_DIR = 'pictures';
    const TMP_DIR = 'tmp';
    const IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg' ];

    const STORED_ZIP_NAME = 'pictures.zip';
    const STORED_CSV_NAME = 'data.csv';

    /** @var LoggerInterface */
    private $logger;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var string */
    private $jobsBasePath;

    /**
     * ImportPicturesService constructor.
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $entityManager
     * @param string $jobsBasePath
     */
    public function __construct(LoggerInterface $logger,
                                EntityManagerInterface $entityManager,
                                string $jobsBasePath)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->jobsBasePath = $jobsBasePath;
    }

    /**
     * @param IPicturesJobsFilter $filter
     * @return ImportPicturesJob[]
     */
    public function getJobs( IPicturesJobsFilter $filter ) {
        /** @var ImportPicturesJobRepository $repository */
        $repository = $this->entityManager->getRepository(ImportPicturesJob::class);
        $jobs = $repository->getByFilter($filter);

        return $jobs;
    }

    public function handleJobs() {
        $this->logger->debug('ImportPicturesService.handleJobs called' );
    }

    public function registerJob(string $name, UploadedFile $zipfile=null, UploadedFile $csvfile=null ) {
        /** @var ImportPicturesJobRepository $repository */
        $repository = $this->entityManager->getRepository(ImportPicturesJob::class);

        if ( $zipfile == null && $csvfile == null ) {
            throw new CatalogValidateException('zip file nor csv file is not given!');
        }

        $job = $repository->createNewJob($name, new DateTime());
        $this->logger->debug('created new job '.$job->getId() );

        $this->createJobDirectory($job);
        $this->storeZipData($job, $zipfile);
        $this->storeCsvFile($job, $csvfile);

        $job->setStatus(ImportPicturesJob::STATUS_CREATED );
        $this->entityManager->persist($job);
        $this->entityManager->flush();

        return $job->getId();
    }

    public function getJobDirectory(ImportPicturesJob $job) {
        return $this->jobsBasePath. DIRECTORY_SEPARATOR. $job->getId();
    }

    public function getPicturesDirectory(ImportPicturesJob $job) {
        return $this->getJobDirectory($job). DIRECTORY_SEPARATOR. self::PICTURES_DIR;
    }

    public function getTmpDirectory(ImportPicturesJob $job) {
        return $this->getJobDirectory($job). DIRECTORY_SEPARATOR. self::TMP_DIR;
    }

    public function createJobDirectory ( ImportPicturesJob $job ) {
        $dir = $this->getJobDirectory($job);
        mkdir ( $dir );
    }

    public function storeZipData (ImportPicturesJob $job, UploadedFile $zipFile=null) {
        if ( $zipFile == null ) {
            return;
        }
        // čia tik nukopijuojam
        $jobdir =  $this->getJobDirectory($job);
        $targetFile = $jobdir . DIRECTORY_SEPARATOR . self::STORED_ZIP_NAME;
        $this->logger->debug('Copying zip from  '.$zipFile->getRealPath().' to '.$targetFile );
        copy ( $zipFile->getRealPath(), $targetFile );
        $job->setOriginalZipFile($zipFile->getFilename());
    }


    public function storeCsvFile(ImportPicturesJob $job, UploadedFile $csvfile=null) {
        if ( $csvfile == null ) {
            return;
        }
        // čia tik nukopijuojam
        $jobdir =  $this->getJobDirectory($job);
        $targetFile = $jobdir . DIRECTORY_SEPARATOR . self::STORED_CSV_NAME;
        $this->logger->debug('Copying csv from  '.$csvfile->getRealPath().' to '.$targetFile );
        copy ( $csvfile->getRealPath(), $targetFile );
        $job->setOriginalCsvFile($csvfile->getFilename());
    }


    // this will be called from command
    public function extractZipFile (ImportPicturesJob $job) {
        $zipArchiveObj = new \ZipArchive();

        $jobdir =  $this->getJobDirectory($job);
        $zipFile = $jobdir . DIRECTORY_SEPARATOR . self::STORED_ZIP_NAME;

        if ( ! $zipArchiveObj->open($zipFile) ) {
            throw new CatalogValidateException('Failed to open zip file '.$zipFile.' uploaded from '.$job->getOriginalZipFile() );
        }

        $tmpDir = $this->getTmpDirectory($job);
        mkdir($tmpDir);
        $zipArchiveObj->extractTo($tmpDir);
        $zipArchiveObj->close();

        $picturesDir = $this->getPicturesDirectory($job);
        mkdir($picturesDir);

        // copy files from tmp tree to flat pictures dir
        $files = scandir($tmpDir);
        foreach ($files as $file ) {
            if ( is_file($file) &&
                ValidateHelper::endsWithAnyOf(strtolower($file), self::IMAGE_EXTENSIONS) )
            {
                $targetFileName = ProductsHelper::fixFileName( basename($file) );
                $targetFilePath = $picturesDir . DIRECTORY_SEPARATOR . $targetFileName;

                $sourceFilePath = $tmpDir .DIRECTORY_SEPARATOR . $file;
                $this->logger->debug('Copying picture from '.$sourceFilePath.' to '.$targetFilePath);
                copy ( $sourceFilePath, $targetFileName );
            }
            else {
                $this->logger->debug('skipping file '.$file.' from archive' );
            }
        }
    }
}