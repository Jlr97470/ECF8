<?php
 
namespace App\Form;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;


use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThan;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;

use App\Entity\Commande;


class CommandeType extends AbstractType
{
    public function addNumeroCommandeField(FormBuilderInterface $builder)
    {
        // Numero de commande
        $builder->add('numerocommande', TextType::class, [
            'label' => 'Numéro de commande',
            'attr' => ['placeholder' => 'Numéro de commande'],
            'required' => true,
            'constraints' => [
                new NotBlank(
                    message: 'Ce champ ne peut être vide'
                ), new Length(
                    // max length allowed by Symfony for security reasons
                    max: 50,
                )
            ]
        ]);
    }    

    public function addDateCommandeField(FormBuilderInterface $builder)
    {
        // Date de la commande
        $builder->add('datecommande', DateType::class, [
            'label' => 'Date de la commande',
            'format' => 'dd-MM-yyyy',
            'attr' => ['placeholder' => 'Date de la commande'],
            'required' => true,
            'constraints' => [
                new NotBlank(
                    message: 'Ce champ ne peut être vide'
                )
            ]
        ]);
    }    
   
    public function addPrixMenuField(FormBuilderInterface $builder)
    {
        // Prix menu      
    }   
    
    public function addPrixLivraisonField(FormBuilderInterface $builder)
    {
        // Prix de livraison
    }   
    
    public function addStatutField(FormBuilderInterface $builder)
    {
        $builder->add('statut', ChoiceType::class, [
            'label' => 'Statut',
            'attr' => ['placeholder' => 'Statut'],
            'required' => true,
            'choices' => [
                'En cours' => 'En cours',
                'Accepté' => 'Accepté',
                'En préparation' => 'En préparation',
                'En cours de livraison' => 'En cours de livraison',
                'Livré' => 'Livré',
                'En attente du retour de matériel' => 'En attente du retour de matériel',
                'Terminé' => 'Terminé',
                'Annulé' => 'Annulé',
            ],
            'constraints' => [
                new NotBlank(
                    message: 'Ce champ ne peut être vide'
                ),
            ],
        ]); 
    }  
    
    public function addRestitutionMaterielField(FormBuilderInterface $builder)
    {
        // Restitution matériel
        $builder->add('restitutionmateriel', CheckboxType::class, [
            'label' => 'Restitution du matériel',
            'required' => false,        
        ]);
    }     
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'] ?? null;
        
        if ($user && (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_USE', $user->getRoles()))) {
            $this->addNumeroCommandeField($builder);
            $this->addDateCommandeField($builder);
            $this->addPrixMenuField($builder);
            $this->addPrixLivraisonField($builder);
            $this->addStatutField($builder);
            $this->addRestitutionMaterielField($builder);
        }        

        // Date de la prestation
        $builder->add('dateprestation', DateType::class, [
            'label' => 'Date de la prestation',
            'format' => 'dd-MM-yyyy',
            'attr' => ['placeholder' => 'Date de la prestation'],
            'required' => false,
            'constraints' => [
                new NotBlank(      
                    message: 'Ce champ ne peut être vide'
                )
            ]
        ]);

        // Nombre de personnes
        $builder->add('nombrepersonne', NumberType::class, [      
            'label' => 'Nombre de personnes',
            'attr' => ['placeholder' => 'Nombre de personnes'],
            'required' => true,
            'constraints' => [
                new NotBlank(
                    message: 'Ce champ ne peut être vide'
                ), new GreaterThan(
                    value: 0,
                    message: 'Le nombre de personnes doit être supérieur à 0'
                )
            ]
        ]);

        $prixMenuOptions = [
            'label' => 'Prix du Menu',
            'attr' => ['placeholder' => 'Prix du Menu'],
            'currency' => 'EUR',
            'constraints' => [
                new GreaterThan(
                    value: 0,
                    message: 'Le prix du Menu doit être supérieur à 0'
                )
            ],
        ];

        if ($user && \in_array('ROLE_USER', $user->getRoles())) {
            $prixMenuOptions['attr']['disabled'] = true; // Désactiver le champ pour les rôles ROLE_USER
        }

        $builder->add('prixmenu', MoneyType::class, $prixMenuOptions);

        $prixLivraisonOptions =[
            'label' => 'Prix de livraison',
            'attr' => ['placeholder' => 'Prix de livraison'],       
            'currency' => 'EUR',            
            'constraints' => [
                new GreaterThan(   
                    value: 0,
                    message: 'Le prix de livraison doit être supérieur à 0'
                )
            ],
        ];

        if ($user && \in_array('ROLE_USER', $user->getRoles())) {
            $prixLivraisonOptions['attr']['disabled'] = true; // Désactiver le champ pour les rôles ROLE_USER
        }

        $builder->add('prixlivraison', MoneyType::class, $prixLivraisonOptions);        

        $builder->add('heurelivraison', TextType::class, [
            'label' => 'Heure de livraison',
            'attr' => ['placeholder' => 'HH:MM'],      
            'required' => true,
            'constraints' => [
                new NotBlank(
                    message: 'Ce champ ne peut être vide'
                ), new Length(        
                    // max length allowed by Symfony for security reasons
                    max: 50,
                ),
                new Regex(
                    pattern: '/^([01]?[0-9]|2[0-3]):[0-5][0-9]$/',
                    message: 'Veuillez entrer une heure au format HH:MM',
                ),
            ]
        ]);
        // Pret matériel
        $builder->add('pretmateriel', CheckboxType::class, [
            'label' => 'Prêt de matériel',
            'required' => true,        
        ]);

        // Bouton Envoyer
        $builder->add('submit', SubmitType::class, array(
            'label' => 'Enregistrer'
        ));    
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
            'user' => null,
        ]);
    }

}
