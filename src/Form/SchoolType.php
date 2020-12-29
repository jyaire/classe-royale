<?php

namespace App\Form;

use App\Entity\School;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SchoolType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => "Nom de l'école",
            ])
            ->add('address', TextType::class, [
                'label' => 'Adresse',
            ])
            ->add('postalCode', TextType::class, [
                'label' => 'Code Postal',
            ])
            ->add('city', TextType::class, [
                'label' => 'Ville',
            ])
            ->add('country', TextType::class, [
                'label' => 'Pays',
            ])
            ->add('phone', TextType::class, [
                'label' => 'Téléphone',
            ])
            ->add('rne', TextType::class, [
                'label' => 'RNE',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => School::class,
        ]);
    }
}
