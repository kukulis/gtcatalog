<?php
/**
 * ImagesImportController.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-20
 * Time: 16:51
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Form\PicturesJobFilterFormType;
use Gt\Catalog\Services\ImportPicturesService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    public function jobCancel() {
        // TODO
    }
}