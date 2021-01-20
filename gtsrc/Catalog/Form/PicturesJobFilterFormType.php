<?php
/**
 * PicturesJobFilterFormType.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-30
 * Time: 15:30
 */

namespace Gt\Catalog\Form;


use Gt\Catalog\Data\IPicturesJobsFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class PicturesJobFilterFormType extends AbstractType implements IPicturesJobsFilter
{
    private $limit=20;
    private $status;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('limit'    , TextType::class, ['required'=>false])
            ->add('status' , TextType::class, ['required'=>false] )
            ->add('show'         , SubmitType::class, ['label'=>'Rodyti'])
        ;
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
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status): void
    {
        $this->status = $status;
    }
}