<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.8.8
 * Time: 07.30
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Exception\CatalogErrorException;
use Gt\Catalog\Exception\CatalogValidateException;
use Gt\Catalog\Form\ClassificatorsListFilterType;
use Gt\Catalog\Services\ClassificatorsService;
use Gt\Catalog\Services\LanguagesService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;

class ClassificatorsController extends AbstractController
{
    public function listAction(Request $request, LoggerInterface $logger, ClassificatorsService $classificatorsService) {

        $classificatorsFilter = new ClassificatorsListFilterType();

        $groups = $classificatorsService->getAllGroups();
        $classificatorsFilter->setAvailableGroups( $groups );
        $form = $this->createForm(ClassificatorsListFilterType::class, $classificatorsFilter);

        $form->handleRequest($request);

        $classificators = $classificatorsService->searchClassificators ( $classificatorsFilter );

        return $this->render('@Catalog/classificators/list.html.twig', [
            'form' => $form->createView(),
            'classificators' => $classificators,
        ]);

    }

    public function importFormAction(Request $request, LanguagesService $languagesService) {

        $languages = $languagesService->getLanguages();
        return $this->render('@Catalog/classificators/import_form.html.twig', [
            'languages' => $languages,
        ]);
    }

    /**
     * @param Request $request
     * @param ClassificatorsService $classificatorsService
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function importAction(Request $request, ClassificatorsService $classificatorsService) {
        /** @var UploadedFile $file */
        $file = $request->files->get('csvfile' );

        $languageCode =  $request->get('language');
        try {
            if ( empty($languageCode)) {
                throw new CatalogErrorException('languageCode not given' );
            }

            if ( empty($file) ) {
                return $this->render('@Catalog/error/error.html.twig', [
                    'error'=> 'Nepaduotas csv failas',
                ]);
            }

            $classificatorsService->importClassificators($file->getRealPath(), $languageCode);
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

        return $this->render('@Catalog/classificators/import_results.html.twig', [
        ]);
    }
}