<?php

namespace App\Form;

use App\Entity\Blocs;
use App\Entity\Specialites;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints as Assert;

class SpecialitesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom',TextType::class,[
                'constraints'=>[
                    new NotBlank(),
                    new Assert\Length(['min'=>2,'max'=>100,'minMessage'=>'Veuillez entrer un nom valide min 3 et max 100'])
                ]
            ])
            ->add('description',TextType::class,[
                'constraints'=>[
                    new NotBlank(),
                    new Assert\Length(['min'=>2,'max'=>100,'minMessage'=>'Veuillez entrer un nom valide min 3 et max 100'])
                ]
            ])
            ->add('duree_consultation',IntegerType::class,[
                'constraints'=>[
                    new NotBlank(),
                    new Assert\Range([
                        'min'=>20,
                        'max'=>90,
                        'notInRangeMessage'=>'Veuillez entrer un nombre valide min 20 et max 90'
                    ])
                ]
            ])
            ->add('tarif',IntegerType::class,[
                'constraints'=>[
                    new NotBlank(),
                    new Assert\GreaterThan([
                        'value'=>39,
                        'message'=>'Veuillez entrer un nombre valide min 40'
                    ])
                ]
            ])
            ->add('statut', ChoiceType::class, [
                'choices' => [
                    'actif' => 'Actif',
                    'inactif' => 'Inactif',
                ]
            ])
            ->add('id_bloc', EntityType::class, [
                'class' => Blocs::class,
                'choice_label' => 'nom',
                'label' => 'Bloc&nbsp',
                'label_html'=> true
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Enregistrer'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Specialites::class,
        ]);
    }
}
