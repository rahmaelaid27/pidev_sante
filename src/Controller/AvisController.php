<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Reponse;
use App\Form\AvisType;
use App\Form\ReponseType;
use App\Repository\AvisRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/avis')]
final class AvisController extends AbstractController
{
    #[Route('/list', name: 'app_avis_list')]
    public function list(AvisRepository $avisRepository, Request $request): Response
    {
        $avisList = $avisRepository->findAll();

        // Créer un formulaire vide pour la réponse
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        return $this->render('avis/index.html.twig', [
            'avisList' => $avisList,
            'form' => $form->createView(), // Passer le formulaire à la vue
        ]);
    }

    #[Route('/new', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();

        $avi = new Avis();
        $avi->setUser($user);
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            dump('Form submitted'); // Debugging
            dump($form->isValid()); // Debugging
            dump($form->getErrors(true)); // Debugging
        }

        if ($form->isSubmitted() && $form->isValid()) {
            dump($avi); // Debugging: Check if the Avis object is populated correctly
            $entityManager->persist($avi);
            $entityManager->flush();

            return $this->redirectToRoute('app_avis_new', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('avis/new.html.twig', [
            'avi' => $avi,
            'form' => $form,
            'success' => $form->isSubmitted() && $form->isValid(),
        ]);
    }

    #[Route('/{avisId}/details', name: 'avis_details')]
    public function details(int $avisId, EntityManagerInterface $entityManager)
    {
        // Récupérer l'avis par ID
        $avis = $entityManager->getRepository(Avis::class)->find($avisId);

        if (!$avis) {
            throw $this->createNotFoundException('Avis not found');
        }

        // Récupérer les réponses liées à cet avis
        $reponses = $entityManager->getRepository(Reponse::class)->findBy(['avis' => $avis]);

        return $this->render('avis/details.html.twig', [
            'avis' => $avis,
            'reponses' => $reponses,
        ]);
    }
    #[Route('/delete/{avisId}', name: 'delete_avis')]
    public function delete(AvisRepository $avisRepository, EntityManagerInterface $entityManager, $avisId): RedirectResponse
    {
        // Fetch the review by its ID
        $avis = $avisRepository->find($avisId);

        if ($avis && $this->getUser() === $avis->getUser()) {
            // Remove the review from the database if it belongs to the logged-in user
            $entityManager->remove($avis);
            $entityManager->flush();

            // Add a flash message to confirm the deletion
            $this->addFlash('success', 'Review deleted successfully.');
        } else {
            // Add a flash message for permission issues or non-existing review
            $this->addFlash('error', 'You are not allowed to delete this review or it does not exist.');
        }

        // Redirect back to the review list page
        return $this->redirectToRoute('app_avis_list');
    }

    #[Route('/edit/{avisId}', name: 'edit_avis')]
    public function edit(
        Request $request,
        AvisRepository $avisRepository,
        EntityManagerInterface $entityManager,
        $avisId
    ): Response {
        $avis = $avisRepository->find($avisId);

        // Vérifier si l'avis existe et appartient à l'utilisateur connecté
        if (!$avis || $this->getUser() !== $avis->getUser()) {
            $this->addFlash('error', 'Vous ne pouvez pas modifier cet avis.');
            return $this->redirectToRoute('app_avis_list');
        }

        // Créer le formulaire d'édition
        $form = $this->createForm(AvisType::class, $avis, ['is_edit' => true]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Avis mis à jour avec succès.');
            return $this->redirectToRoute('app_avis_list');
        }

        return $this->render('avis/edit.html.twig', [
            'form' => $form->createView(),
            'avis' => $avis
        ]);
    }



//    #[Route('/{id}', name: 'app_avis_show', methods: ['GET'])]
//    public function show(Avis $avi): Response
//    {
//        return $this->render('avis/show.html.twig', [
//            'avi' => $avi,
//        ]);
//    }
//
//    #[Route('/{id}/edit', name: 'app_avis_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Avis $avi, EntityManagerInterface $entityManager): Response
//    {
//        $form = $this->createForm(AvisType::class, $avi);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('avis/edit.html.twig', [
//            'avi' => $avi,
//            'form' => $form,
//        ]);
//    }
//
//    #[Route('/{id}', name: 'app_avis_delete', methods: ['POST'])]
//    public function delete(Request $request, Avis $avi, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$avi->getRef(), $request->getPayload()->getString('_token'))) {
//            $entityManager->remove($avi);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('app_avis_index', [], Response::HTTP_SEE_OTHER);
//    }
}
