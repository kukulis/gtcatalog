<?php
/**
 * ImagesImportController.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-20
 * Time: 16:51
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Entity\ImportPicturesJob;
use Gt\Catalog\Exception\CatalogBaseException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Form\PicturesJobFilterFormType;
use Gt\Catalog\Services\ImportPicturesService;
use Gt\Catalog\Services\PicturesService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ImagesImportController extends AbstractController
{
    public function jobList(Request  $request, ImportPicturesService $importPicturesService, LoggerInterface $logger) {
        $logger->debug('jobList called' );

        $picturesJobFilter = new PicturesJobFilterFormType();
        $form = $this->createForm(PicturesJobFilterFormType::class, $picturesJobFilter );
        $form->handleRequest($request);
        $jobs = $importPicturesService->getJobs($picturesJobFilter);

        return $this->render('@Catalog/jobs/list.html.twig', [
            'jobs' => $jobs,
            'filterForm' => $form->createView(),
        ]);
    }

    public function jobAddForm() {
        return $this->render('@Catalog/jobs/add.html.twig', [
        ]);
    }

    public function jobAdd( Request $request, ImportPicturesService $importPicturesService, LoggerInterface $logger ) {
        try {
            $name = $request->get('name');
            $zipfile = $request->files->get('zipfile');
            $csvfile = $request->files->get('csvfile');

            $jobId = $importPicturesService->registerJob($name, $zipfile, $csvfile);
            $logger->debug('Job added '.$jobId );

            return $this->redirectToRoute('gt.catalog.job_list' );
        } catch (CatalogValidateException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    public function jobView($id, ImportPicturesService $importPicturesService) {
        $job = $importPicturesService->getJob($id);

        if ( $job == null ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => "Can't find job by id ".$id,
            ]);
        }
        return $this->render('@Catalog/jobs/edit.html.twig', [
            'job' => $job,
        ]);

    }

    public function jobDelete($id, ImportPicturesService $importPicturesService) {
        $job = $importPicturesService->getJob($id);
        if ( $job == null ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => "Can't find job by id ".$id,
            ]);
        }

        if ( $job->getStatus() == ImportPicturesJob::STATUS_PROCESSING ||
            $job->getStatus() == ImportPicturesJob::STATUS_IN_QUEUE
        ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => "Can't delete job ".$id.' in status '.$job->getStatus(),
            ]);
        }

        $importPicturesService->deleteJob ( $job );
        return $this->redirectToRoute('gt.catalog.job_list');
    }

    public function viewCsv ($id, ImportPicturesService $importPicturesService) {
        $job = $importPicturesService->getJob($id);
        if ( $job == null ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => "Can't find job by id ".$id,
            ]);
        }

        $csvContent = $importPicturesService->getCsvContent($job);

        return new Response(
            $csvContent,
            200,
            [
                'Content-Type' => 'text/csv',
                'Content-Disposition' => sprintf('attachment; filename="%s"', $job->getOriginalCsvFile()),
            ]
        );
    }


    public function jobCancel() {
        // TODO
    }

    public function importImagesFormMeta() {
        return $this->render('@Catalog/pictures/images_meta_form.html.twig',
            [
            ]
        );
    }

    /**
     * @param Request $request
     * @param PicturesService $picturesService
     * @return Response
     */
    public function importImagesMeta(Request $request, PicturesService $picturesService)  {
        try {
            /** @var  UploadedFile $csvFileObject */
            $csvFileObject = $request->files->get('csvfile');
            $count = $picturesService->importPicturesMeta($csvFileObject->getRealPath(), $csvFileObject->getFilename());
            return $this->render('@Catalog/pictures/images_meta_import_result.html.twig',
                [
                    'count' => $count,
                ]
            );
        } catch (CatalogBaseException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}