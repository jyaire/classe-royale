<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class UserAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('email', TextType::class, [
                'label' => 'Adresse Mail',
            ])
            ->add('roles', ChoiceType::class, [
                'required' => true,
                'multiple' => true,
                'expanded' => true,
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Parent' => 'ROLE_PARENT',
                    'Enseignant' => 'ROLE_TEACHER',
                    'Directeur' => 'ROLE_DIRECTOR',
                    'Admin' => 'ROLE_ADMIN',
                    'Super Administrateur' => 'ROLE_SUPER_ADMIN',
                ],
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom de famille',
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('isWoman', ChoiceType::class, [
                'label' => 'Je suis',
                'choices'  => [
                    'une femme' => true,
                    'un homme' => false,
                ],
            ])
            ->add('isVerified');
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
