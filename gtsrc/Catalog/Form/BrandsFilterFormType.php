<?php
/**
 * BrandsFilterFormType.php
 * Created by Giedrius Tumelis.
 * Date: 2021-03-15
 * Time: 13:20
 */

namespace Gt\Catalog\Form;


use Gt\Catalog\Data\IBrandsFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class BrandsFilterFormType extends AbstractType implements IBrandsFilter
{
    private $offset=0;
    private $limit=100;
    private $likeName='';
    private $startsLike;

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('likeName', TextType::class, ['required'=>false])
            ->add('startsLike', TextType::class, ['required'=>false])
            ->add('offset', IntegerType::class )
            ->add('limit', IntegerType::class  )
            ->add('search', SubmitType::class );

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
    public function getStartsLike()
    {
        return $this->startsLike;
    }

    /**
     * @param mixed $startsLike
     */
    public function setStartsLike($startsLike): void
    {
        $this->startsLike = $startsLike;
    }
}