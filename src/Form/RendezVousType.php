<?php

namespace App\Form;

use App\Entity\RendezVous;
use App\Entity\IdUser;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;


class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
{
    $builder
        ->add('date_rdv', DateType::class, [
            'widget' => 'single_text',
            'label' => 'Date du Rendez-vous'
        ])
        ->add('professional', EntityType::class, [ 
            'class' => IdUser::class,
            'choice_label' => 'nameUser',
            'label' => 'Choisir un professionnel',
            'query_builder' => function (EntityRepository $er) { 
                return $er->createQueryBuilder('u')
                    ->where("u.role LIKE :role")
                    ->setParameter('role', 'professionnel');
            },
            'placeholder' => 'Sélectionner un professionnel',
        ]);
}

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}
