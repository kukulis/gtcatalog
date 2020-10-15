<?php
/**
 * ProductsRestController.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-15
 * Time: 11:45
 */

namespace Gt\Catalog\Rest\Controllers;


use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductsLegacyRestController extends AbstractController{
    public function getPrekesAction(Request $r, LoggerInterface $logger) {
        $content = $r->getContent();
        $data = json_decode($content);
        $logger->debug('getPrekesAction called '.var_export($data, true) );
        return new Response( '["labas"]' );
    }
}