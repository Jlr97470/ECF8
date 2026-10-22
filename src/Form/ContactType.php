<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\Email;

use App\Entity\Contact;

class ContactType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
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
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom',
                'attr' => ['placeholder' => 'Prénom'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),                    
                    new Length(
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
                    ]),                    
                    new Length(
                        // max length allowed by Symfony for security reasons
                        max: 50,
                    ),
                ],
            ])
            ->add('titre', TextType::class, [
                'label' => 'Titre',
                'attr' => ['placeholder' => 'Titre'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),                    
                    new Length(
                        // max length allowed by Symfony for security reasons
                        max: 50,
                    ),
                ],
            ])            
            ->add('message', TextareaType::class, [
                'label' => 'Message',
                'attr' => ['placeholder' => 'Message'],
                'required' => true,
                'constraints' => [
                    new NotBlank([
                        'message' => 'Ce champ ne peut être vide',
                    ]),                    
                    new Length(
                        // max length allowed by Symfony for security reasons
                        max: 255,
                    ),
                ],
            ]);

        // Bouton Envoyer
        $builder->add('submit', SubmitType::class, array(
            'label' => 'Envoyer'
        ));            
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Contact::class,
        ]);
    }
}   
