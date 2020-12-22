<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Nom complet',
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse Mail'
            ])
            ->add('subject', ChoiceType::class, [
                'label' => 'Objet du message (choisissez)',
                'choices'  => [
                    "Je veux vous remercier" => 'merci',
                    "J'ai besoin d'aide" => 'aide',
                    "J'ai trouvé un bug" => 'bug',
                    "Je suggère une fonctionalité" => 'suggestion',
                    "Je veux rejoindre l'équipe" => 'job',
                    "Autre question" => 'autre',
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Votre message',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
