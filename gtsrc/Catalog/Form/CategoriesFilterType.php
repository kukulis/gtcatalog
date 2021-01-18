<?php
/**
 * CategoriesFilterType.php
 * Created by Giedrius Tumelis.
 * Date: 2020-10-13
 * Time: 09:21
 */

namespace Gt\Catalog\Form;

use Gt\Catalog\Data\CategoriesFilter;
use Gt\Catalog\Entity\Language;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class CategoriesFilterType extends AbstractType implements CategoriesFilter
{
    const DEFAULT_LANGUAGE_CODE = 'en';

    /** @var Language */
    private $language;
    private $likeCode;
    private $likeParent;
    private $exactParent;

    private $limit=100;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('language', EntityType::class, [
            'class' => Language::class,
            'choice_label' => 'name'])
            ->add('likeCode', TextType::class, ['required'=>false])
            ->add('likeParent', TextType::class, ['required'=>false])
            ->add('exactParent', TextType::class, ['required'=>false])
            ->add( 'limit', IntegerType::class  )
            ->add('search', SubmitType::class );

        $builder->setMethod('get' );
    }


    /**
     * @return Language
     */
    public function getLanguage()
    {
        return $this->language;
    }

    /**
     * @param Language $language
     */
    public function setLanguage($language): void
    {
        $this->language = $language;
    }

    /**
     * @return mixed
     */
    public function getLikeCode()
    {
        return $this->likeCode;
    }

    /**
     * @param mixed $likeCode
     */
    public function setLikeCode($likeCode): void
    {
        $this->likeCode = $likeCode;
    }

    /**
     * @return mixed
     */
    public function getLikeParent()
    {
        return $this->likeParent;
    }

    /**
     * @param mixed $likeParent
     */
    public function setLikeParent($likeParent): void
    {
        $this->likeParent = $likeParent;
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

    /**
     * @return string
     */
    public function getLanguageCode() {
        if ( $this->language != null ) {
            return $this->language->getCode();
        }
        return self::DEFAULT_LANGUAGE_CODE;
    }

    /**
     * @return mixed
     */
    public function getExactParent()
    {
        return $this->exactParent;
    }

    /**
     * @param mixed $exactParent
     */
    public function setExactParent($exactParent): void
    {
        $this->exactParent = $exactParent;
    }
}