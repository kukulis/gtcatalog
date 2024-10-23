<?php

namespace Gt\Catalog\EventListener;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Exception\NotSupported;
use Gt\Catalog\DTO\ProductLanguageLogDto;
use Gt\Catalog\DTO\ProductLogDto;
use Gt\Catalog\Entity\ProductCategory;
use Gt\Catalog\Entity\ProductLog;
use Gt\Catalog\Event\ProductRemoveEvent;
use Gt\Catalog\Event\ProductStoredEvent;
use Gt\Catalog\Exception\ProductLogException;
use Gt\Catalog\Repository\ProductLogRepository;
use http\Exception;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;
use Symfony\Component\Serializer\Serializer;


class ProductLogOnChangeListener
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

    /**
     * @throws ProductLogException
     */
    public function postUpdate(ProductStoredEvent $event): void
    {
        $product = $this->getData($event->getProduct());
        $productOld = $this->getData($event->getOldProduct());
        $productLanguage = $this->getLanguageData($event->getProductLanguage());
        $productLanguageOld = $this->getLanguageData($event->getProductLanguageOld());

        $productLog = new ProductLog();
        $productLog->setLanguage($event->getLanguageCode());
        $productLog->setProductNew($product);
        $productLog->setProductOld($productOld);
        $productLog->setProductLanguage($productLanguage);
        $productLog->setProductLanguageOld($productLanguageOld);
        $productLog->setUser($this->security->getUser());
        $productLog->setSku($event->getProduct()->getSku());
        $productLog->setDateCreated(new \DateTime());

        try {
            /** @var ProductLogRepository $productRepository */
            $productRepository = $this->entityManager->getRepository(ProductLog::class);
            $productRepository->save($productLog);
        } catch (\Exception $e) {
            throw new ProductLogException('Negalima išsaugoti log\'o. Klaida: ' . $e->getMessage());
        }
    }

    /**
     * @throws NotSupported
     */
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

    public function getData(object $product): string
    {
        $data = new ProductLogDto();

        $data->setSku($product->getSku());
        $data->setBrand($product->getBrand());
        $data->setVersion($product->getVersion());
        $data->setLine($product->getLine());
        $data->setParentSku($product->getParentSku());
        $data->setBarcode($product->getBarcode());
        $data->setOriginCountryCode($product->getOriginCountryCode());
        $data->setVersion($product->getVersion());
        $data->setManufacturer($product->getManufacturer());
        $data->setTypeCode($product->getTypeCode());
        $data->setPurposeCode($product->getPurposeCode());
        $data->setMeasureCode($product->getMeasureCode());
        $data->setColor($product->getColor());
        $data->setForMale($product->getForMale());
        $data->setForFemale($product->getForFemale());
        $data->setPackSize($product->getPackSize());
        $data->setPackAmount($product->getPackAmount());
        $data->setWeight($product->getWeight());
        $data->setWeightBruto($product->getWeightBruto());
        $data->setLength($product->getLength());
        $data->setHeight($product->getHeight());
        $data->setWidth($product->getWidth());
        $data->setDeliveryTime($product->getDeliveryTime());
        $data->setGoogleProductCategoryId($product->getGoogleProductCategoryId());
        $data->setPriority($product->getPriority());
        $data->setIngredients($product->getIngredients());
        $data->setCodeFromCustom($product->getCodeFromCustom());

        return $this->serialize->serialize($data, 'json');
    }

    public function getLanguageData($dataLanguage): string
    {
        $data = new ProductLanguageLogDto();

        $data->setName($dataLanguage->getName());
        $data->setShortDescription($dataLanguage->getShortDescription());
        $data->setDescription($dataLanguage->getDescription());
        $data->setLabel($dataLanguage->getLabel());
        $data->setLabelSize($dataLanguage->getLabelSize());
        $data->setLabelMaterial($dataLanguage->getProduct()->getLabelMaterial());
        $data->setVariantName($dataLanguage->getVariantName());
        $data->setInfoProvider($dataLanguage->getInfoProvider());
        $data->setTags($dataLanguage->getTags());

        return $this->serialize->serialize($data, 'json');
    }
}