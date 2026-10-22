<?php
 
namespace App\Form;

use Symfony\Component\Validator\Constraints\Regex;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

use App\Entity\Utilisateur;

class UtilisateurType extends AbstractType
{
    public function addRoles(FormBuilderInterface $builder)
    {
        $builder->add('roles', ChoiceType::class, [
                'label' => 'Rôles',
                'attr' => ['placeholder' => 'Rôles'],
                'required' => true,
                'multiple' => true,
                'choices' => [
                    'Utilisateur' => 'ROLE_USER',
                    'Employée' => 'ROLE_USE',
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                ],
            ]);
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Titre
       $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                'attr' => ['placeholder' => 'Email'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide'
                    ]),
                    new Length(
                        // max length allowed by Symfony for security reasons
                        max: 50,
                    ),
                    new Email([
                        'message' => 'Veuillez entrer une adresse email valide',
                    ]),               
                ],
            ])
            ->add('password', PasswordType::class, [
                'label' => 'Mot de passe',
                'required' => true,
                'attr' => ['autocomplete' => 'new-password'],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),
                    new Length(
                        min: 10,
                        minMessage: 'Votre mot de passe doit avoir au moins {{ limit }} caractères',
                        // max length allowed by Symfony for security reasons
                        max: 50,
                        maxMessage: 'Votre mot de passe ne peut pas avoir plus de {{ limit }} caractères',
                    ),
                    new Regex(
                        pattern: '/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).+$/',
                        message: 'Votre mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et un caractère spécial',
                    )
                ],
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Prénom'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),                    new Length(
                        // max length allowed by Symfony for security reasons
                        max: 50,
                    ),
                ],
            ])
            ->add('nom', TextType::class, [
                'label' => 'Nom',
                'attr' => ['placeholder' => 'Nom'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),                    new Length(
                        // max length allowed by Symfony for security reasons
                        max: 50,
                    ),
                ],
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone', 
                'attr' => ['placeholder' => 'Téléphone'],
                'required' => false,
                'constraints' => [
                    new Length(
                        // max length allowed by Symfony for security reasons
                        max: 50,
                    ),
                ],
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville', 
                'attr' => ['placeholder' => 'Ville'],
                'required' => false,
                'constraints' => [
                    new Length(
                        // max length allowed by Symfony for security reasons
                        max: 50,
                    ),
                ],
            ])
            ->add('pays', TextType::class, [
                'label' => 'Pays',  
                'attr' => ['placeholder' => 'Pays'],
                'required' => false,
                'constraints' => [
                    new Length(
                        // max length allowed by Symfony for security reasons
                        max: 50,
                    ),
                ],
            ])
            ->add('adresse_postal', TextareaType::class, [
                'label' => 'Adresse postale',   
                'attr' => ['placeholder' => 'Adresse postale'],
                'required' => false,
                'constraints' => [
                    new Length(
                        // max length allowed by Symfony for security reasons
                        max: 50,
                    ),
                ],
            ])
        ;
        if ($options['user']
            && $options['data'] instanceof Utilisateur
            && $options['user']->getUtilisateurId() !== $options['data']->getUtilisateurId()
            && in_array('ROLE_ADMIN', $options['user']->getRoles())
        ) {
            $this->addRoles($builder);
        }
        // Bouton Envoyer
        $builder->add('submit', SubmitType::class, array(
            'label' => 'Enregistrer'
        ));            

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Utilisateur::class,
            'user' => null, // Ajout de l'option 'user' pour passer l'utilisateur connecté
            'mode' => 'new', // Ajout de l'option 'mode' pour différencier les modes d'ajout et de modification
        ]);
    }

}
