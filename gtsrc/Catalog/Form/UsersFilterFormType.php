<?php
/**
 * UsersFilterFormType.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-28
 * Time: 16:29
 */

namespace Gt\Catalog\Form;


use Gt\Catalog\Data\IUsersFilter;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UsersFilterFormType extends AbstractType implements IUsersFilter
{
    private $limit=20;
    private $likeName;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('likeName', TextType::class, ['required'=>false] )
            ->add('limit', IntegerType::class)
            ->add('search', SubmitType::class );

        $builder->setMethod('get' );
    }

    public function getLimit()
    {
        return $this->limit;
    }

    public function getLikeName()
    {
        return $this->likeName;
    }

    /**
     * @param mixed $limit
     */
    public function setLimit($limit): void
    {
        $this->limit = $limit;
    }

    /**
     * @param mixed $likeName
     */
    public function setLikeName($likeName): void
    {
        $this->likeName = $likeName;
    }
}