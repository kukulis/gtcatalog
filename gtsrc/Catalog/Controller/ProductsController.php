<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.6.24
 * Time: 15.17
 */

namespace Gt\Catalog\Controller;


use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

class ProductsController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var Environment */
    private $twig;

    /**
     * ProductsController constructor.
     * @param LoggerInterface $logger
     * @param Environment $twig
     */
    public function __construct(LoggerInterface $logger, Environment $twig)
    {
        $this->logger = $logger;
        $this->twig = $twig;
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function listAction(Request $request ) {
        $content = $this->twig->render('@Catalog/products/list.html.twig', [
            'list' => ['aaa', 'bbb', 'ccc'],
        ]);

        return new Response($content);
    }
}