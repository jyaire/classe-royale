<?php

namespace App\Form;

use App\Entity\Section;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ine', TextType::class, [
                'label' => 'Numéro INE (identifiant national)',
            ])
            ->add('firstname', TextType::class, [
                'label' => 'Prénom',
            ])
            ->add('lastname', TextType::class, [
                'label' => 'Nom de famille',
            ])
            ->add('birthdate',DateType::class, [
                    'widget' => 'single_text',
                    'label' => 'Date de naissance',
                ])
            ->add('isGirl', ChoiceType::class, [
                'label' => 'L\'élève est',
                'choices'  => [
                    'une fille' => true,
                    'un garçon' => false,
                ],
                'expanded' => true,
                'multiple' => false,
            ])
            ->add('section', EntityType::class, [
                'class' => Section::class,
                'choice_label' => 'abbreviation',
                'label' => 'Niveau',
                'expanded' => true,
                'multiple' => false,
        ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Student::class,
        ]);
    }
}
