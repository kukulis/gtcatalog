<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.9.4
 * Time: 21.17
 */

namespace Gt\Catalog\Controller;


use Gt\Catalog\Services\PicturesService;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class PicturesController  extends AbstractController
{

    public function uploadPicture(Request $r, LoggerInterface $logger, PicturesService $picturesService) {
        // TODO

        $sku=$r->get('sku', 0 );

        return new Response('TODO upload picture for '.$sku );
    }


}