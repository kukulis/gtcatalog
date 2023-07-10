<?php

namespace Gt\Catalog\Form;

use Gt\Catalog\Data\ProductsFilter;
use Gt\Catalog\Entity\Language;
use Gt\Catalog\Services\ProductsService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
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

    private ?string $dateFrom = '';
    private ?string $dateTill = '';
    private ?string $brand = '';
    private ?string $category = '';

    private ?bool $noLabel = false;


    private $limit = 100;

    /**
     * @var int changed in the setter
     */
    private $maxCsvLimit=10000;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var ProductsFilterType $data */
        $data = $options['data'];

        $builder->add('likeSku', TextType::class, ['required' => false])
            ->add('likeName', TextType::class, ['required' => false])
            ->add('limit', IntegerType::class)
            ->add('dateFrom', TextType::class, ['required' => false])
            ->add('dateTill', TextType::class, ['required' => false])
            ->add('brand', TextType::class, ['required' => false])
            ->add('category', TextType::class, ['required' => false])
            ->add('noLabel', CheckboxType::class, ['required' => false])
            ->add(
                'language',
                EntityType::class,
                [
                    'class' => Language::class,
                    'choice_label' => 'name'
                ]
            )
            ->add('search', SubmitType::class)
            ->add('csv', SubmitType::class, ['label'=>sprintf('Export to csv max %s', $data->getMaxCsvLimit())])
        ;

        $builder->setMethod('get');
    }

    /**
     * @return string
     */
    public function getLikeSku(): ?string
    {
        return $this->likeSku;
    }

    /**
     * @param string $likeSku
     */
    public function setLikeSku(string $likeSku = null): void
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
    public function setLikeName(string $likeName = null): void
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
    public function setLanguage(Language $language = null): void
    {
        $this->language = $language;
    }


    public function getLanguageCode()
    {
        if ($this->language == null) {
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

    public function getDateFrom(): ?string
    {
        return $this->dateFrom;
    }

    public function setDateFrom(?string $dateFrom): void
    {
        $this->dateFrom = $dateFrom;
    }

    public function getDateTill(): ?string
    {
        return $this->dateTill;
    }

    public function setDateTill(?string $dateTill): void
    {
        $this->dateTill = $dateTill;
    }

    public function getBrand(): ?string
    {
        return $this->brand;
    }

    public function setBrand(?string $brand): void
    {
        $this->brand = $brand;
    }

    public function getCategory(): ?string
    {
        return $this->category;
    }

    public function setCategory(?string $category): void
    {
        $this->category = $category;
    }

    public function getNoLabel(): ?bool
    {
        return $this->noLabel;
    }

    public function setNoLabel(?bool $noLabel): void
    {
        $this->noLabel = $noLabel;
    }

    public function getMaxCsvLimit()
    {
        return $this->maxCsvLimit;
    }

    public function setMaxCsvLimit($maxCsvLimit): void
    {
        $this->maxCsvLimit = $maxCsvLimit;
    }
}