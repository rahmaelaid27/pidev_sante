<?php
namespace App\Form;

use App\Entity\RendezVous;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;

class RendezVousType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('id_patient', TextType::class, [
                'label' => 'Patient ID',
                'attr' => ['class' => 'form-control'],
                'required' => true
            ])
            ->add('date_rendez_vous', DateType::class, [
                'label' => 'Date of Appointment',
                'widget' => 'single_text',
                'attr' => ['class' => 'form-control'],
                'required' => true
            ])
            ->add('statu_paiement', ChoiceType::class, [
                'label' => 'Payment Status',
                'choices' => [
                    'Paid' => 'paid',
                    'Pending' => 'pending',
                    'Failed' => 'failed',
                ],
                'placeholder' => 'Select Payment Status', // Empty choice at the top
                'attr' => ['class' => 'form-control'],
                'required' => true
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}
?>
