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

    private $enabled;
    private $rolesstr;

    private $password;
    private $password2;

    private $editorAdmin=false;

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        /** @var UserEditFormType $data */
        $data = $options['data'];

        $builder
            ->add( 'id', TextType::class, ['disabled'=>true] )
            ->add( 'email', TextType::class, ['disabled'=>true] )
            ->add( 'rolesstr', TextType::class, ['disabled'=> ! $data->isEditorAdmin()] )
            ->add( 'enabled', CheckboxType::class, ['required'=>false, 'disabled'=> ! $data->isEditorAdmin()] )
            ->add( 'name', TextType::class, ['label'=>'Vardas (ir Pavardė)'] )
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

    /**
     * @return mixed
     */
    public function getEnabled()
    {
        return $this->enabled;
    }

    /**
     * @param mixed $enabled
     */
    public function setEnabled($enabled): void
    {
        $this->enabled = $enabled;
    }

    /**
     * @param mixed $rolesstr
     */
    public function setRolesstr($rolesstr): void
    {
        $this->rolesstr = $rolesstr;
    }

    /**
     * @return mixed
     */
    public function getRolesstr()
    {
        return $this->rolesstr;
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

    public function getName() {
        return $this->user->getName();
    }

    public function setName ( $name ) {
        $this->user->setName($name);
    }

    /**
     * @return bool
     */
    public function isEditorAdmin(): bool
    {
        return $this->editorAdmin;
    }

    /**
     * @param bool $editorAdmin
     */
    public function setEditorAdmin(bool $editorAdmin): void
    {
        $this->editorAdmin = $editorAdmin;
    }
}