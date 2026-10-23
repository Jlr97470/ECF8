<?php
 
namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\File as FileConstraint;

use App\Entity\Plat;
use Symfony\Component\Form\Extension\Core\Type\FileType;

class PlatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Titre
        $builder->add('titre_plat', TextType::class, [
            'label' => 'Titre du plat',
            'attr' => ['placeholder' => 'Titre du plat'],
            'required' => true,
            'constraints' => [
                new NotBlank(
                    message: 'Ce champ ne peut être vide'
                )
            ]
        ]);

        // nombre de personnes minimum
        $builder->add('file', FileType::class, [
            'label' => 'Photo du plat (1 Mo max)',
            'attr' => ['placeholder' => 'Photo du plat (1 Mo max)'],
            'constraints' => [
                new FileConstraint(
                    maxSize: '1M',
                    mimeTypes: [
                        'image/jpeg',
                        'image/png',
                        'image/gif'
                    ],
                    mimeTypesMessage: 'Veuillez télécharger une image valide (JPEG, PNG ou GIF)',
                    maxSizeMessage: 'La taille maximale du fichier est de 1 Mo'
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
            'data_class' => Plat::class,
        ]);
    }

}
