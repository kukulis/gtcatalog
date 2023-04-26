<?php
/**
 * CustomsKeywordsController.php
 * Created by Giedrius Tumelis.
 * Date: 2021-04-07
 * Time: 12:03
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Form\CustomsKeywordsFormType;
use Gt\Catalog\Services\AutoAssignCustomsNumbersByKeywordsService;
use Gt\Catalog\Services\CustomsKeywordsService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomsKeywordsController  extends AbstractController
{
    /**
     * @param Request $request
     * @param CustomsKeywordsService $customsKeywordsService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function listAction(Request $request, CustomsKeywordsService $customsKeywordsService) {
        $filterType = new CustomsKeywordsFormType();

        $form = $this->createForm(CustomsKeywordsFormType::class, $filterType);
        $form->handleRequest($request);


        $customsKeywords = $customsKeywordsService->getKeywords($filterType);
        return $this->render('@Catalog/customs/keywords_list.html.twig', [
            'form' => $form->createView(),
            'customs_keywords' => $customsKeywords,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importFormAction() {
        return $this->render('@Catalog/customs/keywords_import_form.html.twig', [
        ]);
    }

    /**
     * @param Request $request
     * @param CustomsKeywordsService $customsKeywordsService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importAction(Request $request, CustomsKeywordsService $customsKeywordsService) {
        /** @var UploadedFile $file */
        $file = $request->files->get('csvfile');
        try {
            if (empty($file)) {
                throw new CatalogErrorException('Csv file is not given!');
            }
            $count = $customsKeywordsService->importKeywordsFromCsvFile($file->getRealPath(), $file->getClientOriginalName());
            return $this->render('@Catalog/customs/keywords_import_result.html.twig', [
                'count' =>$count,
            ]);
        } catch (CatalogErrorException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error'=> $e->getMessage(),
            ]);
        }
        catch ( CatalogValidateException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error'=> 'Validavimo klaida:' . $e->getMessage(),
            ]);
        }
    }

    /**
     * @param $id
     * @param CustomsKeywordsService $customsKeywordsService
     * @return Response
     */
    public function deleteAction($id, CustomsKeywordsService $customsKeywordsService) {
        try {
            $customsKeywordsService->deleteKeyword($id);
            return $this->redirectToRoute('gt.catalog.customs.keywords_list');
        }
        catch ( CatalogValidateException $e ) {
            return $this->render('@Catalog/error/error.html.twig', [
                'error'=> 'Validavimo klaida:' . $e->getMessage(),
            ]);
        }
    }

    /**
     * @param AutoAssignCustomsNumbersByKeywordsService $autoAssignCustomsNumbersByKeywordsService
     * @return Response
     * @throws \Doctrine\DBAL\Exception
     */
    public function showAssignmentPrognoze(AutoAssignCustomsNumbersByKeywordsService $autoAssignCustomsNumbersByKeywordsService) {
        $limit = 100;
        $data = $autoAssignCustomsNumbersByKeywordsService->showUpdates($limit);
        return $this->render(
            '@Catalog/customs/show_keywords_assignements.html.twig',
            [
                'data' => $data,
                'limit' => $limit
            ]
        );
    }

}