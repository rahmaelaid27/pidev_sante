<?php

namespace App\Form;

use App\Entity\Facture;
use App\Entity\Rendez;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FactureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('montant')
            ->add('date', null, [
                'widget' => 'single_text'
            ])
            ->add('rendez', EntityType::class, [
                'class' => Rendez::class,
                'choice_label' => 'id',
                'label' => 'Appointment',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Facture::class,
        ]);
    }
}
