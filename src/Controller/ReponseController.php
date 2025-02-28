<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Reponse;
use App\Form\ReponseType;
use App\Repository\AvisRepository;
use App\Repository\ReponseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class ReponseController extends AbstractController
{

    #[Route('/avis/{avisId}/respond', name: 'respond_avis', methods: ['GET', 'POST'])]
    public function respondToAvis(Request $request, int $avisId, EntityManagerInterface $entityManager): Response
    {
        $avis = $entityManager->getRepository(Avis::class)->find($avisId);

        if (!$avis) {
            throw $this->createNotFoundException('Avis not found');
        }

        $professional = $this->getUser();

        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reponse->setAvis($avis);
            $reponse->setMadeBy($professional);
            $reponse->setProfessional($professional);
            $reponse->setDateReponse(new \DateTime());
            $entityManager->persist($reponse);
            $entityManager->flush();

            return $this->redirectToRoute('app_avis_list');
        }

        return $this->render('avis/respond.html.twig', [
            'avis' => $avis,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reponse/update/{id}', name:'update_response', methods: ['GET', 'POST'])]
    public function updateResponse(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        $reponse = $entityManager->getRepository(Reponse::class)->find($id);

        if (!$reponse) {
            throw $this->createNotFoundException('Response not found');
        }
        $avis = $reponse->getAvis();

        if ($this->getUser() !== $reponse->getAvis()->getUser()) {

            throw $this->createAccessDeniedException('You cannot edit this response.');
        }

        $form = $this->createForm(ReponseType::class, $reponse);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('avis_details', ['avisId' => $reponse->getAvis()->getRef()]);
        }

        return $this->render('reponse/edit.html.twig', [
            'form' => $form->createView(),
            'reponse' => $reponse,
        ]);
    }

    #[Route('/reponse/delete/{id}', name: 'delete_response', methods: ['POST'])]
    public function deleteResponse(int $id, EntityManagerInterface $entityManager): Response
    {
        $reponse = $entityManager->getRepository(Reponse::class)->find($id);

        if (!$reponse) {
            throw $this->createNotFoundException('Response not found');
        }

        if ($this->getUser() !== $reponse->getProfessional()) {
            throw $this->createAccessDeniedException('You cannot delete this response.');
        }

        $entityManager->remove($reponse);
        $entityManager->flush();

        return $this->redirectToRoute('avis_details', ['avisId' => $reponse->getAvis()->getId()]);
    }






}
