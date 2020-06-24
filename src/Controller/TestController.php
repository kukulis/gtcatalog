<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 18.04
 */

namespace App\Controller;


use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    /**
     * @param  LoggerInterface $logger
     * @return Response
     * @Route("/test")
     */
    public function testAction (LoggerInterface $logger) {
        $logger->error('testAction called' );
        return new Response('TODO test' );
    }




}