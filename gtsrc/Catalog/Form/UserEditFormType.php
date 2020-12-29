<?php
/**
 * UserEditFormType.php
 * Created by Giedrius Tumelis.
 * Date: 2020-12-29
 * Time: 11:00
 */

namespace Gt\Catalog\Form;


use Gt\Catalog\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;

class UserEditFormType extends AbstractType
{
    /**
     * Wrapping, because not all field are allowed to modify + not all fields are strings
     * @var User
     */
    private $user;

    private $password;
    private $password2;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add( 'id', TextType::class, ['disabled'=>true] )
            ->add( 'email', TextType::class, ['disabled'=>true] )
            ->add( 'rolesstr', TextType::class )
            ->add( 'enabled', CheckboxType::class, ['required'=>false] )
            ->add( 'password', PasswordType::class, ['required'=>false] )
            ->add( 'password2', PasswordType::class, ['required'=>false] )

            ->add('save', SubmitType::class );
    }

    /**
     * @param User $user
     */
    public function setUser(User $user): void
    {
        $this->user = $user;
    }


    public function getId() {
        return $this->user->getId();
    }

    public function getEmail() {
        return $this->user->getEmail();
    }

    public function getRolesstr() {
        return $this->user->getRolesStr();
    }

    public function isEnabled() {
        return $this->user->isEnabled();
    }


//    public function setId( $id ) {
//        $this->user->setId($id);
//    }
//
//    public function setEmail($email) {
//        $this->user->setEmail($email);
//    }

    public function setRolesstr($str) {
        $roles = explode ( ',', $str );
        $rolesClean = array_map ( 'trim', $roles );
        $rolesFiltered = array_filter($rolesClean, function($role) {return !empty($role);});
        $this->user->setRoles($rolesFiltered);
    }

    public function setEnabled($enabled) {
        $this->user->setEnabled($enabled);
    }

    /**
     * @return mixed
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * @param mixed $password
     */
    public function setPassword($password): void
    {
        $this->password = $password;
    }

    /**
     * @return mixed
     */
    public function getPassword2()
    {
        return $this->password2;
    }

    /**
     * @param mixed $password2
     */
    public function setPassword2($password2): void
    {
        $this->password2 = $password2;
    }
}