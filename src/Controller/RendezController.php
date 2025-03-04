<?php

namespace App\Controller;

use App\Entity\Rendez;
use App\Form\RendezForm;
use App\Repository\RendezRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/rendez')]
class RendezController extends AbstractController
{
    #[Route('/', name: 'rendez_index', methods: ['GET'])]
    public function index(RendezRepository $rendezRepository): Response
    {
        $user = $this->getUser();

        $rendezs = $rendezRepository->findBy(['user' => $user]);

        return $this->render('rendez_vous/index.html.twig', [
            'rendezs' => $rendezs,
        ]);
    }

    #[Route('/new', name: 'rendez_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, LoggerInterface $logger): Response
    {
        // Get the currently logged-in user
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'You must be logged in to create an appointment.');
            return $this->redirectToRoute('front_login');
        }

        $rendez = new Rendez();
        $rendez->setUser($user);

        $form = $this->createForm(RendezForm::class, $rendez);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rendez);
            $entityManager->flush();

            $this->addFlash('success', 'Appointment created successfully.');
            return $this->redirectToRoute('rendez_index');
        }

        return $this->render('rendez_vous/new.html.twig', [
            'rendez' => $rendez,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'rendez_show', methods: ['GET'])]
    public function show(Rendez $rendez): Response
    {
        return $this->render('rendez_vous/show.html.twig', [
            'rendez' => $rendez,
        ]);
    }

    #[Route('/admin/rendez/{id}/edit', name: 'admin_rendez_edit', methods: ['GET', 'POST'])]
    public function adminEdit(Request $request, Rendez $rendez, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RendezForm::class, $rendez);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('admin_rendez_list');
        }

        return $this->render('admin/rendez/edit.html.twig', [
            'form' => $form->createView(),
            'rendez' => $rendez,
        ]);
    }

    #[Route('/admin/rendez/{id}', name: 'admin_rendez_delete', methods: ['POST'])]
    public function adminDelete(Request $request, Rendez $rendez, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete' . $rendez->getId(), $request->request->get('_token'))) {
            $entityManager->remove($rendez);
            $entityManager->flush();
            $this->addFlash('success', 'Appointment deleted successfully.');
        } else {
            $this->addFlash('error', 'Invalid CSRF token.');
        }

        return $this->redirectToRoute('admin_rendez_list');
    }


    #[Route('/admin/rendez', name: 'admin_rendez_list')]
    public function adminList(Request $request, RendezRepository $rendezRepository): Response
    {
        $search = $request->query->get('search');
        $sortBy = $request->query->get('sort_by', 'id');
        $order  = $request->query->get('order', 'ASC');

        $qb = $rendezRepository->createQueryBuilder('r')
            ->join('r.user', 'u');

        if ($search) {
            $qb->andWhere('u.nameUser LIKE :search OR r.statuPaiement LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($sortBy === 'user.nameUser') {
            $qb->orderBy('u.nameUser', $order);
        } else {
            $qb->orderBy('r.' . $sortBy, $order);
        }

        $rendezs = $qb->getQuery()->getResult();

        return $this->render('admin/rendez/index.html.twig', [
            'rendezs' => $rendezs,
        ]);
    }

    #[Route('/admin/rendez/stats', name: 'admin_rendez_stats', methods: ['GET'])]
    public function adminStats(Request $request, RendezRepository $rendezRepository, EntityManagerInterface $entityManager): Response
    {
        $paymentQuery = $entityManager->createQuery(
            'SELECT r.statuPaiement, COUNT(r.id) as count FROM App\Entity\Rendez r GROUP BY r.statuPaiement'
        );
        $paymentData = $paymentQuery->getResult();

        $visitsQuery = $entityManager->createQuery(
            'SELECT MONTH(r.dateRendez) as month, COUNT(r.id) as count FROM App\Entity\Rendez r GROUP BY month'
        );
        $visitsData = $visitsQuery->getResult();

        $radarQuery = $entityManager->createQuery(
            'SELECT p.nameUser as professional, COUNT(r.id) as count FROM App\Entity\Rendez r JOIN r.professional p GROUP BY p.id'
        );
        $radarData = $radarQuery->getResult();

        $patientQuery = $entityManager->createQuery(
            'SELECT p.id as id, p.nameUser as nameUser, p.email as email, COUNT(r.id) as appointmentCount 
         FROM App\Entity\Rendez r JOIN r.user p GROUP BY p.id, p.nameUser, p.email'
        );
        $patientData = $patientQuery->getResult();

        return $this->render('admin/rendez/statistics.html.twig', [
            'paymentData' => $paymentData,
            'visitsData'  => $visitsData,
            'radarData'   => $radarData,
            'patientData' => $patientData,
        ]);
    }

}
