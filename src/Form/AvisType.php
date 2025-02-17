<?php
namespace App\Form;

use App\Entity\Avis;
use App\Entity\User;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityManagerInterface;

class AvisType extends AbstractType
{
    public function __construct(private EntityManagerInterface $em) {}

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note')
            ->add('commentaire')
            ->add('date_avis', null, [
                'widget' => 'single_text',
            ])
            ->add('professional', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'nom',
                'label' => 'Choisir un professionnel',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->where("u.roles LIKE :role")
                        ->setParameter('role', '%ROLE_PRO%'); // Searches for ROLE_PRO inside JSON array
                },
                'placeholder' => 'Sélectionner un professionnel',
            ])
            ->add('submit', SubmitType::class, ['label' => 'Ajouter']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
