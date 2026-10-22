<?php
 
namespace App\Form;

use App\Entity\Utilisateur;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

class ProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Titre
       $builder
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),
                    new Length(
                        6,
                        12,
                        null,
                        'Votre mot de passe doit avoir au moins {{ limit }} caractères',
                        'Votre mot de passe ne peut pas avoir plus de {{ limit }} caractères'
                    ),
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Prénom'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),                    new Length(null, 50),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Nom'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),                    new Length(null, 50),
                ],
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone', 
                'attr' => ['placeholder' => 'Téléphone'],
                'required' => false,
                'constraints' => [
                    new Length(null, 50),
                ],
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville', 
                'attr' => ['placeholder' => 'Ville'],
                'required' => false,
                'constraints' => [
                    new Length(null, 50),
                ],
            ])
            ->add('pays', TextType::class, [
                'label' => 'Pays',  
                'attr' => ['placeholder' => 'Pays'],
                'required' => false,
                'constraints' => [
                    new Length(null, 50),
                ],
            ])
            ->add('adresse_postal', TextareaType::class, [
                'label' => 'Adresse postale',   
                'attr' => ['placeholder' => 'Adresse postale'],
                'required' => false,
                'constraints' => [
                    new Length(null, 50),
                ],
            ])
        ;
        // Bouton Envoyer
        $builder->add('submit', SubmitType::class, array(
            'label' => 'Enregistrer'
        ));            

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
        ]);
    }

}
