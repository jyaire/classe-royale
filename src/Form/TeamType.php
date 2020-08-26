<?php

namespace App\Form;

use App\Entity\Student;
use App\Entity\Team;
use App\Repository\StudentRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $classgroup = $options['classgroup'];
        $builder
            ->add('name')
            ->add('student', EntityType::class, [
                'class' => Student::class,
                'choice_label' => 'firstname',
                'expanded' => true,
                'multiple' => true,
                'query_builder' => function (StudentRepository $studentRepository) use ($classgroup) {
                    return $studentRepository->createQueryBuilder('s')
                        ->andWhere('s.classgroup = :val')
                        ->setParameter('val', $classgroup)
                        ->orderBy('s.lastname', 'ASC')
                        ;
                }
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Team::class,
        ])
            ->setRequired('classgroup');
    }
}
