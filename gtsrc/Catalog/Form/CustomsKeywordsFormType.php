<?php
/**
 * CustomsKeywordsFolterType.php
 * Created by Giedrius Tumelis.
 * Date: 2021-04-07
 * Time: 13:34
 */

namespace Gt\Catalog\Form;


use Gt\Catalog\Data\CustomsKeywordsFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomsKeywordsFormType extends AbstractType implements CustomsKeywordsFilter
{
    private $offset=0;
    private $limit=30;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('offset', IntegerType::class)
            ->add('limit', IntegerType::class)
            ->add('search', SubmitType::class)
        ;
        $builder->setMethod('get' );
    }

    /**
     * @return mixed
     */
    public function getOffset()
    {
        return $this->offset;
    }

    /**
     * @param mixed $offset
     */
    public function setOffset($offset): void
    {
        $this->offset = $offset;
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