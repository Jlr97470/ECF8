<?php
 
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Validator\Constraints\GreaterThan;


use App\Entity\Horaire;
class HoraireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Titre
        $builder->add('jour', ChoiceType::class, [
            'label' => 'Jour de l\'Horaire',   
            'attr' => ['placeholder' => 'Jour de l\'Horaire'],
            'required' => true,
            'choices' => [
                'Lundi' => 'Lundi',
                'Mardi' => 'Mardi',
                'Mercredi' => 'Mercredi',
                'Jeudi' => 'Jeudi',
                'Vendredi' => 'Vendredi',
                'Samedi' => 'Samedi',
                'Dimanche' => 'Dimanche'
            ],
            'constraints' => [
            new NotBlank(
                message: 'Ce champ ne peut être vide',
            ),
            ]
        ])
        ->add('heureouverture', TextType::class, [
            'label' => 'Heure d\'ouverture',   
            'attr' => ['placeholder' => 'HH:MM'],
            'required' => true,
            'constraints' => [
            new NotBlank(
                message: 'Ce champ ne peut être vide',
            ),
            new Length(
                // max length allowed by Symfony for security reasons
                max: 50,
                maxMessage: 'Votre libellé ne peut pas avoir plus de {{ limit }} caractères',
            ),
            new Regex(
                pattern: '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
                message: 'Veuillez entrer une heure au format HH:MM',
            ),
            ]
        ])
        ->add('heurefermeture', TextType::class, [
            'label' => 'Heure de fermeture',   
            'attr' => ['placeholder' => 'HH:MM'],
            'required' => true,
            'constraints' => [
            new NotBlank(
                message: 'Ce champ ne peut être vide',
            ),
            new Length(
                // max length allowed by Symfony for security reasons
                max: 50,
                maxMessage: 'Votre libellé ne peut pas avoir plus de {{ limit }} caractères',
            ),
            new Regex(
                pattern: '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
                message: 'Veuillez entrer une heure au format HH:MM',
            ),
            ]
        ]);
        // Bouton Envoyer
        $builder->add('submit', SubmitType::class, array(
            'label' => 'Enregistrer'
        ));        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Horaire::class,
        ]);
    }

}
