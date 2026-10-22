<?php
 
namespace App\Form;

use App\Entity\Menu;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\PositiveOrZero;

class MenuType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Titre
        $builder->add('titre', TextType::class, [
            'label' => 'Titre',
            'attr' => ['placeholder' => 'Titre du menu'],
            'required' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Ce champ ne peut être vide'
                ]), new Length(
                    // max length allowed by Symfony for security reasons
                    max: 50,
                )
            ]
        ]);

        // nombre de personnes minimum
        $builder->add('nombrepersonneminimum', NumberType::class, [
            'label' => 'Nombre de personnes minimum',
            'attr' => ['placeholder' => 'Nombre de personnes minimum'],
            'required' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Ce champ ne peut être vide'
                ]),
                new GreaterThan([
                    'value' => 0,
                    'message' => 'Le nombre de personnes minimum doit être supérieur à 0'
                ])
            ]
        ]);

        // prix par personne
        $builder->add('prixparpersonne', MoneyType::class, [
            'label' => 'Prix par personne',
            'attr' => ['placeholder' => 'Prix par personne'],
            'required' => true,
            'currency' => 'EUR',            
            'constraints' => [
                new NotBlank([
                    'message' => 'Ce champ ne peut être vide'
                ]),
                new GreaterThan([
                    'value' => 0,
                    'message' => 'Le prix par personne doit être supérieur à 0'
                ])
            ]
        ]);

        // régime
        $builder->add('regime', TextType::class, [  
            'label' => 'Régime',
            'constraints' => [
                new NotBlank([
                    'message' => 'Ce champ ne peut être vide'
                ]), new Length(
                    // max length allowed by Symfony for security reasons
                    max: 50,
                )
            ]
        ]); 
        
        // Contenu
        $builder->add('description', TextareaType::class, [
            'label' => 'Description du menu',
            'attr' => ['placeholder' => 'Description du menu'],
            'required' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Ce champ ne peut être vide'
                ]), new Length(
                    // max length allowed by Symfony for security reasons
                    max: 50,
                )
            ]
        ]);

        // Quantité restante
        $builder->add('quantiterestante', NumberType::class, [
            'label' => 'Quantité restante',
            'attr' => ['placeholder' => 'Quantité restante'],
            'required' => true,
            'constraints' => [
                new NotBlank([
                    'message' => 'Ce champ ne peut être vide'
                ]),
                new PositiveOrZero([
                    'message' => 'La quantité restante doit être supérieure ou égale à 0'
                ])
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
            'data_class' => Menu::class,
        ]);
    }

}
