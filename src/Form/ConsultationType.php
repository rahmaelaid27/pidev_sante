<?php
namespace App\Form;

use App\Entity\Consultation;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\{TextType, TextareaType, DateType};

class ConsultationType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('date_consultation', DateType::class, [
                'constraints' => [
                    new NotBlank(['message' => 'Consultation date is required.']),
                ],
                'widget' => 'single_text',
                'label' => 'Consultation Date',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Select consultation date'
                ]
            ])
            ->add('reason', TextType::class, [
                'required' => false, // Make it optional, since it's nullable
                'constraints' => [
                    new NotBlank(['message' => 'Reason cannot be empty.']),
                    new Length([
                        'min' => 3,
                        'minMessage' => 'The reason must be at least {{ limit }} characters.',
                    ]),
                ],
                'label' => 'Reason for Consultation',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Enter reason (optional)'
                ]
            ])
        ;
    }



    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Consultation::class,
        ]);
    }
}
