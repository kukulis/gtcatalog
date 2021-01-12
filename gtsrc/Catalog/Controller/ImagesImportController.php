<?php
/**
 * ImagesImportController.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-20
 * Time: 16:51
 */

namespace Gt\Catalog\Controller;


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
        // TODO
        return new Response('TODO add import job' );
    }

    public function jobAdd() {
        // TODO
    }

    public function jobView() {
        // TODO
    }

    public function jobCancel() {
        // TODO
    }
}