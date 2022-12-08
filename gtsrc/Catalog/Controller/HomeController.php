<?php


namespace Gt\Catalog\Controller;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * Annotations are not working, the config is in yml files.
     * @Route("/", name="home")
     */
    public function homeAction()
    {
        return $this->render('@Catalog/home/home.html.twig',[
        ]);
    }

    /**
     * Annotations are not working, the config is in yml files.
     * @Route("/info", name="info")
     */
    public function infoAction()
    {
        phpinfo();

        return new Response('');
    }
}