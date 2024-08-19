<?php

namespace Gt\Catalog\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\NotSupported;
use Gt\Catalog\Entity\ProductLog;
use Gt\Catalog\Event\ProductStoredEvent;
use Gt\Catalog\Repository\ProductLogRepository;
use Symfony\Component\Security\Core\Security;

class ProductChangeListener
{
    // TODO (S) įrašyti tipą, gal interfeisą? (Galima išsiaiškinti debuginant)
    private $security;
    private EntityManagerInterface $entityManager;


    public function __construct(EntityManager $entityManager, Security $security)
    {
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    /**
     * // TODO kas čia tas NotSupported ? Ar mums jo reikia?
     * @throws NotSupported
     */
    public function postUpdate(ProductStoredEvent $event): void
    {
        $productLog = new ProductLog();
        $productLog->setLanguage($event->getLanguageCode());
        $productLog->setProductOld($event->getOldProduct());
        $productLog->setProductNew($event->getProduct()->getSku());
        $productLog->setUser($this->security->getUser());
        $productLog->setSku($event->getProduct()->getSku());
        $productLog->setDateCreated(new \DateTime());

        /** @var ProductLogRepository $productRepository */
        $productRepository = $this->entityManager->getRepository(ProductLog::class);
        $productRepository->save($productLog);
    }

    public function postRemove(): void
    {
        // TODO (FF) tokių reiktų nepalikti. Implementuoti?
        die('asd');
    }
}