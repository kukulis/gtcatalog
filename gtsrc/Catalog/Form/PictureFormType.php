<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.7.17
 * Time: 20.25
 */

namespace Gt\Catalog\Form;

use Gt\Catalog\Entity\ProductPicture;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PictureFormType  extends AbstractType
{
    /** @var ProductPicture */
    private $productPicture;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference'    , TextType::class, ['required'=>false])
            ->add('infoProvider' , TextType::class, ['required'=>false] )
            ->add('statusas'     , TextType::class, ['required'=>false] )
            ->add('priority'     , TextType::class)
            ->add('save'         , SubmitType::class, ['label'=>'Saugoti'])
        ;
    }

    /**
     * @param ProductPicture $productPicture
     */
    public function setProductPicture(ProductPicture $productPicture): void
    {
        $this->productPicture = $productPicture;
    }

    public function getPriority() {
        return $this->productPicture->getPriority();
    }

    public function setPriority($p) {
        $this->productPicture->setPriority($p);
    }

    /**
     * @return mixed
     */
    public function getInfoProvider()
    {
        return $this->productPicture->getPicture()->getInfoProvider();
    }

    /**
     * @param mixed $infoProvider
     */
    public function setInfoProvider($infoProvider): void
    {
        $this->productPicture->getPicture()->setInfoProvider( $infoProvider );
    }

    /**
     * @return mixed
     */
    public function getStatusas()
    {
        return $this->productPicture->getPicture()->getStatusas();
    }

    /**
     * @param mixed $version
     */
    public function setStatusas($version): void
    {
        $this->productPicture->getPicture()->setStatusas($version);
    }

    /**
     * @return mixed
     */
    public function getReference()
    {
        return $this->productPicture->getPicture()->getReference();
    }

    /**
     * @param mixed $reference
     */
    public function setReference($reference): void
    {
        $this->productPicture->getPicture()->setReference( $reference );
    }
}