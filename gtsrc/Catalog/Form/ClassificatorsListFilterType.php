<?php
/**
 * Created by PhpStorm.
 * User: giedrius
 * Date: 20.8.8
 * Time: 13.35
 */

namespace Gt\Catalog\Form;


use Gt\Catalog\Data\ClassificatorsListFilter;
use Gt\Catalog\Entity\ClassificatorGroup;
use Gt\Catalog\Entity\Language;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class ClassificatorsListFilterType extends AbstractType implements ClassificatorsListFilter
{
    /**
     * @var ClassificatorGroup[]
     */
    private $availableGroups=[];
    private $groupCode;
    private $likeCode;
    private $likeName;
    private $limit=10;

    /** @var Language */
    private $language;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ClassificatorsListFilterType $data */
        $data = $options['data'];

        $choices = [''=>0];
        foreach ($data->getAvailableGroups() as $g ) {
            $choices[ $g->getName() ] = $g->getCode();
        }

        $builder
            ->add('groupCode', ChoiceType::class, ['choices' => $choices, 'required'=>false])
            ->add('likeCode', TextType::class, ['required'=>false] )
            ->add('likeName', TextType::class, ['required'=>false])
            ->add('limit', IntegerType::class)
            ->add('language', EntityType::class, [
            'class' => Language::class,
                'choice_label' => 'name'
            ])
            ->add('search', SubmitType::class)
        ;

        $builder->setMethod('get' );
    }

    /**
     * @return ClassificatorGroup[]
     */
    public function getAvailableGroups(): array
    {
        return $this->availableGroups;
    }

    /**
     * @param ClassificatorGroup[] $availableGroups
     */
    public function setAvailableGroups(array $availableGroups): void
    {
        $this->availableGroups = $availableGroups;
    }

    /**
     * @return mixed
     */
    public function getGroupCode()
    {
        return $this->groupCode;
    }

    /**
     * @param mixed $groupCode
     */
    public function setGroupCode($groupCode): void
    {
        $this->groupCode = $groupCode;
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
    public function getLikeName()
    {
        return $this->likeName;
    }

    /**
     * @param mixed $likeName
     */
    public function setLikeName($likeName): void
    {
        $this->likeName = $likeName;
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

    public function getLanguageCode(): ?string
    {
        if ( empty($this->language)) {
            return '';
        }

        return $this->language->getCode();
    }
}