<?php
 
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\GreaterThanOrEqual;
use Symfony\Component\Validator\Constraints\LessThanOrEqual;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

use App\Entity\Avis;

class AvisType extends AbstractType
{
    public function addStatutField(FormBuilderInterface $builder)
    {
        $builder->add('statut', ChoiceType::class, [
            'label' => 'Statut de l\'Avis',
            'attr' => ['placeholder' => 'Statut de l\'Avis'],
            'required' => true,
            'choices' => [
                'En Attente' => 'En attente',
                'Validé' => 'Validé',
                'Rejeté' => 'Rejeté',
            ]
        ]);
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $user = $options['user'] ?? null;
        // Titre
        $builder->add('note', TextType::class, [
            'label' => 'Note de l\'Avis',
            'attr' => ['placeholder' => 'Note de l\'Avis'],
            'required' => true,
            'constraints' => [
            new NotBlank([
                'message' => 'Ce champ ne peut être vide',
            ]),
            new GreaterThanOrEqual([
                'value' => 0,
                'message' => 'La note doit être supérieure ou égale à 0',
            ]),
            new LessThanOrEqual([
                'value' => 5,
                'message' => 'La note doit être inférieure ou égale à 5',
            ])
        ]
        ]);

        $builder->add('description', TextType::class, [
            'label' => 'Description de l\'Avis',   
            'attr' => ['placeholder' => 'Description de l\'Avis'],
            'required' => true,
            'constraints' => [
            new NotBlank([
                'message' => 'Ce champ ne peut être vide',
            ]),
            new Length(
                // max length allowed by Symfony for security reasons
                max: 50,
                maxMessage: 'Votre description ne peut pas avoir plus de {{ limit }} caractères',
            ),
            ]
        ]);

        if ($user && (in_array('ROLE_ADMIN', $user->getRoles()) || in_array('ROLE_USE', $user->getRoles()))) {
            $this->addStatutField($builder);
        }
        // Bouton Envoyer
        $builder->add('submit', SubmitType::class, array(
            'label' => 'Enregistrer'
        ));        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
            'user' => null,
        ]);
    }

}
