<?php
/**
 * AdminHomeController.php
 * Created by Giedrius Tumelis.
 * Date: 2021-01-06
 * Time: 08:49
 */

namespace Gt\Catalog\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminHomeController extends AbstractController
{
    public function homeAction() {
        return $this->render('@Catalog/admin/home.html.twig',[
        ]);
    }
}