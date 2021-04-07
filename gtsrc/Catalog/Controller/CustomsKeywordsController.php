<?php
/**
 * CustomsKeywordsController.php
 * Created by Giedrius Tumelis.
 * Date: 2021-04-07
 * Time: 12:03
 */

namespace Gt\Catalog\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;

class CustomsKeywordsController  extends AbstractController
{
    public function listAction(Request $request) {
        return $this->render('@Catalog/customs/keywords_list.html.twig', [
        ]);
    }

    public function importAction(Request $request) {
        return $this->render('@Catalog/customs/keywords_import.html.twig', [
        ]);
    }
}