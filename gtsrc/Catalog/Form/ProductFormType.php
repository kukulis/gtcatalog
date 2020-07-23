<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.7.18
 * Time: 08.39
 */

namespace Gt\Catalog\Form;


use Gt\Catalog\Entity\Language;
use Gt\Catalog\Entity\Product;
use Gt\Catalog\Entity\ProductLanguage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;


/**
 * Class ProductFormType
 * @package Gt\Catalog\Form
 * @method getP_SKU
 * @method setP_SKU (string $sku)
 * @method getPL_Name
 * @method setPL_Name (string $name)
 * @method getPL_Description
 * @method setPL_Description (string $description)
 */
class ProductFormType extends AbstractType
{
    /** @var Product */
    private $product;

    /** @var ProductLanguage */
    private $productLanguage;

    /** @var Language[] */
    private $languages;

    /** @var Language */
    private $selectedLanguage;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('p_sku', TextType::class )
            ->add('language', ChoiceType::class, ['a'=>'a', 'b'=>'b'] ) // TODO languages from options->data
            ->add('pl_name', TextType::class )
            ->add('pl_description', TextType::class );
    }

    public function __call($name, $arguments) {
        if (  strpos( $name, 'getP_') === 0 ) {
            $l = strlen('getP_');
            $namestripped = 'get'.substr( $name, $l);
            return $this->product->{$namestripped}();
        }
        elseif (  strpos( $name, 'getPL_') === 0 ) {
            $l = strlen('getPL_');
            $namestripped = 'get'.substr( $name, $l);
            return $this->productLanguage->{$namestripped}();
        }
        elseif (  strpos( $name, 'setP_') === 0 ) {
            $l = strlen('setP_');
            $namestripped = 'set'.substr( $name, $l);
            return $this->product->{$namestripped}($arguments[0]);
        }
        else if (  strpos( $name, 'setPL_') === 0 ) {
            $l = strlen('setPL_');
            $namestripped = 'set'.substr( $name, $l);
            return $this->productLanguage->{$namestripped}($arguments[0]);
        }
        else {
            throw new \RuntimeException('function '.$name.' not found ');
        }
    }

    /**
     * @return Product
     */
    public function getProduct(): Product
    {
        return $this->product;
    }

    /**
     * @param Product $product
     */
    public function setProduct(Product $product): void
    {
        $this->product = $product;
    }

    /**
     * @return ProductLanguage
     */
    public function getProductLanguage(): ProductLanguage
    {
        return $this->productLanguage;
    }

    /**
     * @param ProductLanguage $productLanguage
     */
    public function setProductLanguage(ProductLanguage $productLanguage): void
    {
        $this->productLanguage = $productLanguage;
    }

    /**
     * @return Language[]
     */
    public function getLanguages(): array
    {
        return $this->languages;
    }

    /**
     * @param Language[] $languages
     */
    public function setLanguages(array $languages): void
    {
        $this->languages = $languages;
    }

    /**
     * @return Language
     */
    public function getSelectedLanguage(): Language
    {
        return $this->selectedLanguage;
    }

    /**
     * @param Language $selectedLanguage
     */
    public function setSelectedLanguage(Language $selectedLanguage): void
    {
        $this->selectedLanguage = $selectedLanguage;
    }

//    public function __set($prop, $val) {
//
//    }


}