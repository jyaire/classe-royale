<?php

namespace App\Form;

use App\Entity\Occupation;
use App\Entity\Student;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OccupationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('student', EntityType::class, [
                'class' => Student::class,
                'label' => "Quels élèves ? (maintenir CTRL pour sélection multiple)",
                'choice_label' => 'firstname',
                'multiple' => true,
                'expanded' => false,
                'mapped' => false,
            ])
            ->add('salary', IntegerType::class, [
                'label' => "Salaire par jour (0 par défaut)",
                'data' => '0',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Occupation::class,
        ]);
    }
}
