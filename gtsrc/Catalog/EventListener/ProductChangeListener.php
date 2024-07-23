<?php

namespace Gt\Catalog\EventListener;

use Doctrine\Persistence\Event\LifecycleEventArgs;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductLog;
use Symfony\Component\Security\Core\Security;

class ProductChangeListener
{
    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    public function postUpdate($product, $event): void
    {
        $em = $event->getEntityManager();
        $uow = $em->getUnitOfWork();

        $changes = $uow->getEntityChangeSet($product);
        $details = json_encode($changes);

        $log = new ProductLog();
        $log->setAction('update');
        $log->setTimestamp(new \DateTime());
        $log->setDetails($details);
        $log->setUsername($this->security->getUser()->getUsername());

        $em->persist($log);
        $em->flush();
    }

    public function postRemove(): void
    {
    }
}