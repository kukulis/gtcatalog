<?php
/**
 * UserAddFormType.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-29
 * Time: 14:55
 */

namespace Gt\Catalog\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserAddFormType  extends AbstractType
{
    private $email;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add( 'email', TextType::class )
            ->add('add', SubmitType::class );
    }

    /**
     * @return mixed
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @param mixed $email
     */
    public function setEmail($email): void
    {
        $this->email = $email;
    }


}