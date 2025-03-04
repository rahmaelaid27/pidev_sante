<?php

namespace App\Form;

use App\Entity\Rendez;
use App\Entity\IdUser;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class RendezForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Get current appointment data (if editing)
        $rendez = $options['data'] ?? null;
        $currentProfessionalId = null;
        if ($rendez && $rendez->getProfessional()) {
            $currentProfessionalId = $rendez->getProfessional()->getId();
        }

        $builder
            ->add('professional', EntityType::class, [
                'class' => IdUser::class,
                'choice_label' => 'nameUser',
                'label' => 'Select Professional',
                'query_builder' => function(EntityRepository $er) use ($currentProfessionalId) {
                    // Use LOWER() to enforce case-insensitive comparison
                    $qb = $er->createQueryBuilder('u')
                        ->where('LOWER(u.role) = :role')
                        ->setParameter('role', 'professionnel');

                    if ($currentProfessionalId) {
                        $qb->orWhere('u.id = :current')
                            ->setParameter('current', $currentProfessionalId);
                    }

                    return $qb;
                },
            ])
            ->add('dateRendez', DateType::class, [
                'label'  => 'Date of Appointment',
                'widget' => 'single_text',
            ])
            ->add('statuPaiement', ChoiceType::class, [
                'label'       => 'Payment Status',
                'choices'     => [
                    'Paid'    => 'paid',
                    'Pending' => 'pending',
                    'Failed'  => 'failed',
                ],
                'placeholder' => 'Select Payment Status',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Rendez::class,
        ]);
    }
}
