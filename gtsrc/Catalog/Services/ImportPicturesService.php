<?php
/**
 * ImportPicturesService.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-30
 * Time: 15:11
 */

namespace Gt\Catalog\Services;


use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Dao\CatalogDao;
use Gt\Catalog\Data\IPicturesJobsFilter;
use Gt\Catalog\Entity\ImportPicturesJob;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Repository\ImportPicturesJobRepository;
use Gt\Catalog\Utils\CsvUtils;
use Gt\Catalog\Utils\FileHelper;
use Gt\Catalog\Utils\ValidateHelper;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use \DateTime;
use \Exception;
use \ZipArchive;

class ImportPicturesService
{
    const PICTURES_DIR = 'pictures';
    const TMP_DIR = 'tmp';
    const IMAGE_EXTENSIONS = ['png', 'jpg', 'jpeg' ];

    const STORED_ZIP_NAME = 'pictures.zip';
    const STORED_CSV_NAME = 'data.csv';

    const MAX_JOBS = 10;

    /** @var LoggerInterface */
    private $logger;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var string */
    private $jobsBasePath;

    /** @var PicturesService */
    private $picturesService;

    /** @var CatalogDao */
    private $catalogDao;

    /**
     * ImportPicturesService constructor.
     * @param LoggerInterface $logger
     * @param EntityManagerInterface $entityManager
     * @param string $jobsBasePath
     */
    public function __construct(LoggerInterface $logger,
        EntityManagerInterface $entityManager,
        string $jobsBasePath,
        PicturesService $picturesService,
        CatalogDao $catalogDao

)
    {
        $this->logger = $logger;
        $this->entityManager = $entityManager;
        $this->jobsBasePath = $jobsBasePath;
        $this->picturesService = $picturesService;
        $this->catalogDao = $catalogDao;
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
        $job->setOriginalZipFile($zipFile->getClientOriginalName());
    }


    public function storeCsvFile(ImportPicturesJob $job, UploadedFile $csvfile=null) {
        if ( $csvfile == null ) {
            return;
        }
        // čia tik nukopijuojam
        $targetFile = $this->getCsvFilePath($job);
        $this->logger->debug('Copying csv from  '.$csvfile->getRealPath().' to '.$targetFile );
        copy ( $csvfile->getRealPath(), $targetFile );
        $job->setOriginalCsvFile($csvfile->getClientOriginalName());
    }


    // this will be called from command
    public function extractZipFile (ImportPicturesJob $job) {
        $zipArchiveObj = new ZipArchive();

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
        @mkdir($picturesDir);

        // copy files from tmp tree to a flat pictures dir
        $files = FileHelper::getFiles($tmpDir);
        foreach ($files as $file ) {
            if ( is_file($file) &&
                ValidateHelper::endsWithAnyOf(strtolower($file), self::IMAGE_EXTENSIONS) )
            {
//                $targetFileName = ProductsHelper::fixFileName( basename($file) ); nefiksinkim

                $targetFileName = basename($file);
                $targetFilePath = $picturesDir . DIRECTORY_SEPARATOR . $targetFileName;

                $sourceFilePath =$file; // nereikia nieko apendinti, nes FileHelper::getFiles grąžina pilnus kelius
                $this->logger->debug('Copying picture from '.$sourceFilePath.' to '.$targetFilePath);
                copy ( $sourceFilePath, $targetFilePath );
            }
            else {
                $this->logger->debug('skipping file '.$file.' from archive' );
            }
        }
    }

    public function handleJobs() {
        /** @var ImportPicturesJobRepository $jobsRepository */
        $jobsRepository = $this->entityManager->getRepository(ImportPicturesJob::class );

        // 1) get jobs
        /** @var ImportPicturesJob[] $jobs */
        $jobs = $jobsRepository->getJobsInStatus(ImportPicturesJob::STATUS_CREATED, self::MAX_JOBS );

        $this->logger->debug('Found jobs '.count($jobs));

        // 2) mark jobs as enqueued
        $jobsRepository->setStatuses( $jobs, ImportPicturesJob::STATUS_IN_QUEUE );

        // 3) handle each job
        foreach ($jobs as $job ) {
            try {
                $this->logger->debug('Working with job '.$job->getId().'  '.$job->getName());
                $this->extractZipFile($job );
                $csvFile = $this->getCsvFilePath ($job);

                if ( !file_exists($csvFile) ) {
                    $job->setStatus(ImportPicturesJob::STATUS_FAIL );
                    $job->setMessage('Import job without csv file is not implemented yet' );
                    continue;
                }

//                $jobsRepository->setStatuses([$job], ImportPicturesJob::STATUS_PROCESSING);
                $job->setStatus(ImportPicturesJob::STATUS_PROCESSING);
                $job->setStartTime(new DateTime());
                $jobsRepository->update($job);
                $messages = $this->importPicturesByCsvFile($job, $csvFile);

                if( count($messages) > 0 ) {
                    $message = join ( "\n", $messages );
                    $job->setMessage($message);
                }

                $job->setFinishedTime(new DateTime());
                $job->setStatus(ImportPicturesJob::STATUS_FINISHED );

            } catch (Exception $e) {
                $this->logger->error('Error importing images by job id '.$job->getId(). '  '.$e->getMessage() );

                $job->setFinishedTime(new DateTime());
                $job->setStatus(ImportPicturesJob::STATUS_FAIL );
                $job->setMessage($e->getMessage());
            } finally {
                if ( $job->getStatus() == ImportPicturesJob::STATUS_PROCESSING ) {
                    $job->setStatus(ImportPicturesJob::STATUS_FAIL);
                }
                $this->entityManager->persist($job);
                $this->entityManager->flush();
            }
        }

        $this->logger->debug('ImportPicturesService.handleJobs called' );
    }

    public function getCsvFilePath (ImportPicturesJob $job ) {
        $jobdir =  $this->getJobDirectory($job);
        $targetFile = $jobdir . DIRECTORY_SEPARATOR . self::STORED_CSV_NAME;
        return $targetFile;
    }

    public function importPicturesByCsvFile ( ImportPicturesJob $job, $csvFile ) {

        $messages = [];

        $f = fopen($csvFile, 'r' );
        $header = fgetcsv($f);

        if ( count($header) < 2 ) {
            throw new CatalogValidateException('In csv file must be at least 2 columns: "sku" and "file", and "priority" (optional)' );
        }

        if ( array_search('sku', $header ) === false ) {
            throw new CatalogValidateException('In csv file must be column "sku"' );
        }

        if ( array_search('file', $header ) === false ) {
            throw new CatalogValidateException('In csv file must be column "file"' );
        }

        $picturesDir = $this->getPicturesDirectory($job);
        $headMap = array_flip($header);
        $line = fgetcsv($f);
        $count = 0;
        $totalCount = 0;
        while ( $line != null ) {
            $totalCount++;
            $lineMap = CsvUtils::arrayToAssoc( $headMap, $line);
            $line = fgetcsv($f); // iš kart kitą eilutę skaitom
            $sourceFile = $lineMap['file'];
            $sku = $lineMap['sku'];
            $job->setLastSku($sku);

            $priority = 1;
            if ( isset($lineMap['priority']) ) {
                $priority = $lineMap['priority'];
            }

            $statusas = null;
            if ( isset($lineMap['statusas']) ) {
                $statusas = $lineMap['statusas'];
            }


            $infoProvider = null;
            if ( isset($lineMap['info_provider']) ) {
                $infoProvider = $lineMap['info_provider'];
            }



            // check if http
            if ( str_starts_with( $sourceFile, 'http://' )
            || str_starts_with( $sourceFile, 'https://' ) ) {
                $sourceFilePath = $sourceFile;

                $sourceFile = basename($sourceFile);
            }
            else {
                $sourceFilePath = $picturesDir . DIRECTORY_SEPARATOR . $sourceFile;
            }

            $product = $this->catalogDao->getProduct($sku);
            if ( $product == null ) {
                $messages[] = 'ERROR: cant find product by sku ['.$sku.']';
                continue;
            }
            try {
                $picture = $this->picturesService->createPicture($sourceFilePath, $sourceFile, false, $statusas, $infoProvider);
                $this->picturesService->unassignPictureByPriority($product->getSku(), $priority); // deleting (unassigning) old picture
                $this->picturesService->assignPictureToProduct($product, $picture, $priority);
                $count++;
            } catch ( CatalogValidateException $exception ) {
                $messages[] = $exception->getMessage();
            }
        }

        $job->setImportedPictures($count);
        $job->setTotalPictures($totalCount);

        $messages[] = "Imported pictures count ".$count;

        return $messages;
    }

    /**
     * @param int $id
     * @return ImportPicturesJob
     */
    public function getJob ( $id ) {
        /** @var ImportPicturesJobRepository $repository */
        $repository = $this->entityManager->getRepository(ImportPicturesJob::class);

        /** @var ImportPicturesJob $job */
        $job  = $repository->find($id);

        return $job;
    }

    public function deleteJob(ImportPicturesJob $job) {
        $dir = $this->getJobDirectory($job);
        system("rm -rf ".escapeshellarg($dir));
        /** @var ImportPicturesJobRepository $jobRepository */
        $jobRepository = $this->entityManager->getRepository(ImportPicturesJob::class);

        $jobRepository->deleteJob($job);
    }

    /**
     * @param ImportPicturesJob $job
     * @return false|string
     */
    public function getCsvContent ( ImportPicturesJob $job) {
        $csvFile = $this->getCsvFilePath($job);
        $content = file_get_contents($csvFile);
        return $content;
    }

}