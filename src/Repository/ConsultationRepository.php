<?php

namespace App\Repository;

use App\Entity\Consultation;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Consultation>
 */
class ConsultationRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Consultation::class);
    }

    public function findByProfessionalAndDateBeforeToday(User $professional): array
    {
        $today = new \DateTime('today');

        return $this->createQueryBuilder('c')
            ->andWhere('c.professionnel = :professional')
            ->andWhere('c.date_consultation < :today')
            ->setParameter('professional', $professional)
            ->setParameter('today', $today)
            ->orderBy('c.date_consultation', 'DESC')
            ->getQuery()
            ->getResult();
    }

    public function findByDate($date)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.date_consultation = :date')
            ->setParameter('date', $date)
            ->getQuery()
            ->getResult();
    }
}
