<?php

namespace Gt\Catalog\Rest\Controllers;

use Catalog\B2b\Common\Data\Rest\RestResult;
use Gt\Catalog\Dao\TmpPackagesDao;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class PackagesRestController
{
    public function getAll(Request $request, TmpPackagesDao $tmpPackagesDao, LoggerInterface $logger) {
        $logger->debug( sprintf('%s ::getAll called', PackagesRestController::class));
        $fromNomnr = $request->get('fromNomnr');
        $limit = $request->get('limit', 500);
        $restResult = new RestResult();
        $restResult->data= $tmpPackagesDao->fetchAll($fromNomnr, $limit);

        return new JsonResponse($restResult);
    }
}