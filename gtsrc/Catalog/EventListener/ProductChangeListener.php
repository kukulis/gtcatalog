<?php

namespace Gt\Catalog\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\NotSupported;
use Gt\Catalog\Entity\ProductLog;
use Gt\Catalog\Event\ProductStoredEvent;
use mysql_xdevapi\DatabaseObject;
use Symfony\Component\Security\Core\Security;

class ProductChangeListener
{
    private $security;
    private EntityManagerInterface $entityManager;


    public function __construct(EntityManager $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * @throws NotSupported
     */
    public function postUpdate(ProductStoredEvent $event): void
    {
        $productLog = new ProductLog();
        $productLog->setLanguage($event->getProductLanguage());
        $productLog->setProductOld($event->getOldProduct());
        $productLog->setProductNew($event->getProduct()->getSku());
        $productLog->setUser($this->security->getUser());
        $productLog->setSku($event->getProduct()->getSku());
        $productLog->setDateCreated(new \DateTime());

        $productRepository = $this->entityManager->getRepository(ProductLog::class);
        $productRepository->save($productLog);
    }

    public function postRemove(): void
    {
        die('asd');
    }
}