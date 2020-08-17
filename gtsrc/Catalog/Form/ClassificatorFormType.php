<?php


namespace Gt\Catalog\Form;


use Gt\Catalog\Entity\Classificator;
use Gt\Catalog\Entity\ClassificatorGroup;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ClassificatorFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // TODO kalbų dropdown'as
        $builder
            ->add('code')
            ->add('group', EntityType::class, [
                'class' => ClassificatorGroup::class,
                'choice_label' => 'name'
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        // nežinau ar šitas tiks, nes kalbų sąrašą paduosiu į ClassificatorFormType, o ne į Classificator
        $resolver->setDefaults([
            'data_class' => Classificator::class
        ]);
    }

}