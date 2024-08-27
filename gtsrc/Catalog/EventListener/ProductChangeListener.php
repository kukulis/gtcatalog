<?php

namespace Gt\Catalog\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Gt\Catalog\Entity\ProductLog;
use Gt\Catalog\Event\ProductRemoveEvent;
use Gt\Catalog\Event\ProductStoredEvent;
use Gt\Catalog\Repository\ProductLogRepository;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Serializer;

// TODO (S) rename ProductLogOnChangeListener
class ProductChangeListener
{
    private Security $security;
    private EntityManagerInterface $entityManager;
    private Serializer $serialize;


    public function __construct(EntityManager $entityManager, Security $security, Serializer $serialize)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
        $this->serialize = $serialize;
    }

    public function postUpdate(ProductStoredEvent $event): void
    {
        $product = $this->serialize->serialize($event->getProduct(), 'json');
        $productOld = $this->serialize->serialize($event->getOldProduct(), 'json');
        $productLanguage = $this->serialize->serialize($event->getProductLanguage(), 'json');
        $productLanguageOld = $this->serialize->serialize($event->getProductLanguageOld(), 'json');

        $productLog = new ProductLog();
        $productLog->setLanguage($event->getLanguageCode());
        $productLog->setProductNew($product);
        $productLog->setProductOld($productOld);
        $productLog->setProductLanguage($productLanguage);
        $productLog->setProductLanguageOld($productLanguageOld);
        $productLog->setUser($this->security->getUser());
        $productLog->setSku($event->getProduct()->getSku());
        $productLog->setDateCreated(new \DateTime());

        /** @var ProductLogRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(ProductLog::class);
        $productRepository->save($productLog);
    }

    public function postRemove(ProductRemoveEvent $event): void
    {
        $productLog = new ProductLog();
        $productLog->setLanguage($event->getLanguageCode());
        $productLog->setUser($this->security->getUser());
        $productLog->setSku($event->getProduct()->getSku());
        $productLog->setDateCreated(new \DateTime());

        /** @var ProductLogRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(ProductLog::class);
        $productRepository->save($productLog);
    }
}