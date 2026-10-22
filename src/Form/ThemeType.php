<?php
 
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Length;

use App\Entity\Theme;
class ThemeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Titre
        $builder->add('libelle', TextType::class, [
            'label' => 'Libellé du Theme',   
            'attr' => ['placeholder' => 'Libellé du Theme'],
            'required' => true,
            'constraints' => [
            new NotBlank(
                message: 'Ce champ ne peut être vide',
            ),
            new Length(
                // max length allowed by Symfony for security reasons
                max: 50,
                maxMessage: 'Votre libellé ne peut pas avoir plus de {{ limit }} caractères',
            )
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
            'data_class' => Theme::class,
        ]);
    }

}
