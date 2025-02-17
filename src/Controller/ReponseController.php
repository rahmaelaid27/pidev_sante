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
//    #[Route( '/reponse',name: 'app_reponse_index', methods: ['GET'])]
//    public function index(ReponseRepository $reponseRepository): Response
//    {
//        return $this->render('reponse/index.html.twig', [
//            'reponses' => $reponseRepository->findAll(),
//        ]);
//    }

//    #[Route('/new', name: 'app_reponse_new', methods: ['GET', 'POST'])]
//    public function new(Request $request, EntityManagerInterface $entityManager): Response
//    {
//        $reponse = new Reponse();
//        $form = $this->createForm(ReponseType::class, $reponse);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->persist($reponse);
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('reponse/new.html.twig', [
//            'reponse' => $reponse,
//            'form' => $form,
//        ]);
//    }

//    #[Route('/{id}', name: 'app_reponse_show', methods: ['GET'])]
//    public function show(Reponse $reponse): Response
//    {
//        return $this->render('reponse/show.html.twig', [
//            'reponse' => $reponse,
//        ]);
//    }

//    #[Route('/{id}/edit', name: 'app_reponse_edit', methods: ['GET', 'POST'])]
//    public function edit(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
//    {
//        $form = $this->createForm(ReponseType::class, $reponse);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $entityManager->flush();
//
//            return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
//        }
//
//        return $this->render('reponse/edit.html.twig', [
//            'reponse' => $reponse,
//            'form' => $form,
//        ]);
//    }

//    #[Route('/{id}', name: 'app_reponse_delete', methods: ['POST'])]
//    public function delete(Request $request, Reponse $reponse, EntityManagerInterface $entityManager): Response
//    {
//        if ($this->isCsrfTokenValid('delete'.$reponse->getId(), $request->getPayload()->getString('_token'))) {
//            $entityManager->remove($reponse);
//            $entityManager->flush();
//        }
//
//        return $this->redirectToRoute('app_reponse_index', [], Response::HTTP_SEE_OTHER);
//    }

//    #[Route('/respond/{avisId}', name: 'respond_avis', requirements: ['reponse' => '\d+'])]
//    public function respond(Request $request, AvisRepository $avisRepository, EntityManagerInterface $em, int $avisId)
//    {
//        $avis = $avisRepository->find($avisId);
//
//        if (!$avis) {
//            throw $this->createNotFoundException('Avis not found');
//        }
//
//        $reponse = new Reponse();
//        $reponse->setAvis($avis);
//        $reponse->setProfessional($this->getUser()); // Assuming the current user is the professional responding
//
//        $form = $this->createForm(ReponseType::class, $reponse);
//        $form->handleRequest($request);
//
//        if ($form->isSubmitted() && $form->isValid()) {
//            $reponse->setDateReponse(new \DateTime());
//            $em->persist($reponse);
//            $em->flush();
//
//            return $this->redirectToRoute('avis_list');
//        }
//        $avisList = $avisRepository->findAll();
//
//        return $this->render('avis/index.html.twig', [
//            'form' => $form->createView(),
//            'avis' => $avis,
//            'avisList' => $avisList,
//        ]);
//    }

    #[Route('/avis/{avisId}/respond', name: 'respond_avis', methods: ['GET', 'POST'])]
    public function respondToAvis(Request $request, int $avisId, EntityManagerInterface $entityManager): Response
    {
        // Fetch the avis based on avisId
        $avis = $entityManager->getRepository(Avis::class)->find($avisId);

        if (!$avis) {
            throw $this->createNotFoundException('Avis not found');
        }

        $professional = $this->getUser();

        // Create a new response entity and form
        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);

        // Handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $reponse->setAvis($avis);
            $reponse->setProfessional($professional);
            $reponse->setDateReponse(new \DateTime());
            $entityManager->persist($reponse);
            $entityManager->flush();

            // Redirect back to the list of avis
            return $this->redirectToRoute('app_avis_list');
        }

        // Render the response form on a new page
        return $this->render('avis/respond.html.twig', [
            'avis' => $avis,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/reponse/update/{id}', name:'update_response', methods: ['GET', 'POST'])]
    public function updateResponse(Request $request, int $id, EntityManagerInterface $entityManager): Response
    {
        // Fetch the response based on its ID
        $reponse = $entityManager->getRepository(Reponse::class)->find($id);

        if (!$reponse) {
            throw $this->createNotFoundException('Response not found');
        }
        $avis = $reponse->getAvis();

        // Check if the logged-in user is the owner of the response
        if ($this->getUser() !== $reponse->getAvis()->getUser()) {

            throw $this->createAccessDeniedException('You cannot edit this response.');
        }

        // Create the form for updating the response
        $form = $this->createForm(ReponseType::class, $reponse);

        // Handle form submission
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            // Update the response (flush the changes)
            $entityManager->flush();

            // Redirect to the corresponding avis page after successful update
            return $this->redirectToRoute('avis_details', ['avisId' => $reponse->getAvis()->getRef()]);
        }

        // Render the update form on the same page
        return $this->render('reponse/edit.html.twig', [
            'form' => $form->createView(),
            'reponse' => $reponse,
        ]);
    }

    // src/Controller/ResponseController.php

    // src/Controller/ResponseController.php

    // src/Controller/ResponseController.php

    #[Route('/reponse/delete/{id}', name: 'delete_response', methods: ['POST'])]
    public function deleteResponse(int $id, EntityManagerInterface $entityManager): Response
    {
        // Fetch the response based on its ID
        $reponse = $entityManager->getRepository(Reponse::class)->find($id);

        if (!$reponse) {
            throw $this->createNotFoundException('Response not found');
        }

        // Check if the logged-in user is the owner of the response
        if ($this->getUser() !== $reponse->getProfessional()) {
            throw $this->createAccessDeniedException('You cannot delete this response.');
        }

        // Delete the response from the database
        $entityManager->remove($reponse);
        $entityManager->flush();

        // Redirect back to the avis details page after deletion
        return $this->redirectToRoute('avis_details', ['avisId' => $reponse->getAvis()->getId()]);
    }






}
