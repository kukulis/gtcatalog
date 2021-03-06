<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.8.29
 * Time: 19.53
 */

namespace Gt\Catalog\Form;


use Gt\Catalog\Data\ProductsFilter;
use Gt\Catalog\Entity\Language;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ProductsFilterType extends AbstractType implements ProductsFilter
{
    /** @var string */
    private $likeSku;

    /** @var string */
    private $likeName;

    /** @var Language */
    private $language;

    private $limit=100;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('likeSku', TextType::class, ['required'=>false] )
            ->add('likeName', TextType::class, ['required' => false])
            ->add('limit', IntegerType::class)
            ->add('language', EntityType::class, [
                'class' => Language::class,
                'choice_label' => 'name'
            ])
            ->add('search', SubmitType::class );

        $builder->setMethod('get' );
    }

    /**
     * @return string
     */
    public function getLikeSku(): ? string
    {
        return $this->likeSku;
    }

    /**
     * @param string $likeSku
     */
    public function setLikeSku(string $likeSku=null): void
    {
        $this->likeSku = $likeSku;
    }

    /**
     * @return string
     */
    public function getLikeName(): ?string
    {
        return $this->likeName;
    }

    /**
     * @param string $likeName
     */
    public function setLikeName(string $likeName=null): void
    {
        $this->likeName = $likeName;
    }

    /**
     * @return Language
     */
    public function getLanguage(): ?Language
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage(Language $language=null): void
    {
        $this->language = $language;
    }


    public function getLanguageCode() {
        if ( $this->language == null ) {
            return 'en';
        }
        return $this->language->getCode();
    }

    /**
     * @return mixed
     */
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit): void
    {
        $this->limit = $limit;
    }
}