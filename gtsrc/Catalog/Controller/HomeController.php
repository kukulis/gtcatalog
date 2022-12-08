<?php


namespace Gt\Catalog\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function homeAction()
    {
        return $this->render('@Catalog/home/home.html.twig',[
        ]);
    }

    /**
     * @Route("/info", name="home")
     */
    public function infoAction()
    {
        phpinfo();

        return new Response('');
    }
}