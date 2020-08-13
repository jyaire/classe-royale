<?php

namespace App\Form;

use App\Entity\Point;
use App\Entity\Student;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PointMultipleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('student', EntityType::class, [
                'class' => Student::class,
                'choice_label' => 'firstname',
                'expanded' => true,
                'multiple' => true,
            ])
            ->add('quantity', EntityType::class, [
                'class' => Point::class,
            ])
            ->add('reason', ReasonType::class);
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
