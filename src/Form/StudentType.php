<?php

namespace App\Form;

use App\Entity\Section;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('ine')
            ->add('firstname')
            ->add('lastname')
            ->add('birthdate',DateType::class, [
                    'widget' => 'single_text',
                ])
            ->add('isGirl')
            ->add('section', EntityType::class, [
                'class' => Section::class,
                'choice_label' => 'abbreviation',
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
