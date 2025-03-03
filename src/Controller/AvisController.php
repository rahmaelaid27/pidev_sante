<?php

namespace App\Controller;

use App\Entity\Avis;
use App\Entity\Reponse;
use App\Form\AvisType;
use App\Form\ReponseType;
use App\Repository\AvisRepository;
use App\Repository\ReponseRepository;
use App\Service\MailerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/avis')]
final class AvisController extends AbstractController
{
    #[Route('/list', name: 'app_avis_list')]
    public function list(AvisRepository $avisRepository, Request $request): Response
    {
        $user = $this->getUser();

        if (in_array("ROLE_PROFESSIONAL", $user->getRoles())) {
            $avisList = $avisRepository->findBy(['professional' => $user]);
        } else {
            $avisList = $avisRepository->findAll();
        }

        $sortBy = $request->query->get('sort_by', 'date_avis');
        $order = $request->query->get('order', 'ASC');

        $queryBuilder = $avisRepository->createQueryBuilder('c')
            ->leftJoin('c.user', 'u');

        if (in_array("ROLE_PROFESSIONAL", $user->getRoles())) {
            $queryBuilder->andWhere('c.professional = :user')
                ->setParameter('user', $user);
        } else {
            $queryBuilder->andWhere('c.professional IS NOT NULL');
        }

        if ($sortBy === 'user.nameUser') {
            $queryBuilder->orderBy('u.nameUser', $order);
        } else {
            $queryBuilder->orderBy('c.' . $sortBy, $order);
        }

        $selectedDate = $request->query->get('selected_date');
        if ($selectedDate) {
            $formattedDate = \DateTime::createFromFormat('m/d/Y', $selectedDate);

            if ($formattedDate) {
                $startOfDay = clone $formattedDate;
                $startOfDay->setTime(0, 0, 0);

                $endOfDay = clone $formattedDate;
                $endOfDay->setTime(23, 59, 59);

                $queryBuilder->andWhere('c.date_avis BETWEEN :startOfDay AND :endOfDay')
                    ->setParameter('startOfDay', $startOfDay)
                    ->setParameter('endOfDay', $endOfDay);
            }
        }

        $avisList = $queryBuilder->getQuery()->getResult();

        $reponse = new Reponse();
        $form = $this->createForm(ReponseType::class, $reponse);
        $form->handleRequest($request);

        return $this->render('avis/index.html.twig', [
            'avisList' => $avisList,
            'form' => $form->createView(),
        ]);
    }



    #[Route('/new', name: 'app_avis_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager, MailerService $mailerService): Response
    {
        $user = $this->getUser();

        $avi = new Avis();
        $avi->setUser($user);
        $form = $this->createForm(AvisType::class, $avi);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $entityManager->persist($avi);
            $entityManager->flush();

            //mailing
            $note=$avi->getNote();
            $commentaire=$avi->getCommentaire();
            $nomPatient=$user->getNameUser();
            $professionalEmail=$avi->getProfessional()->getEmail();
            $professionalNom=$avi->getProfessional()->getNameUser();
            $subject = "Nouvelle Avis de " .$nomPatient;



            $content = "
                <p>Bonjour <strong>{$professionalNom}</strong>,</p>
                <p>Un nouveau avis a été soumis par le patient: <strong>{$nomPatient}</strong></p>                             
                <blockquote style='border-left: 4px solid #007bff; padding-left: 10px; color: #333;'>
                <p><strong>Note:</strong> {$note}</p> 
                <p><strong>Commentaire:</strong> {$commentaire}</p>                    
                </blockquote>
                <p>Pour toute question, n'hésitez pas à contacter le patient ou votre équipe médicale.</p>
                <p>Cordialement,</p>
                <p><em>L'équipe Mediplus</em></p>
            ";

            $mailerService->sendEmail($professionalEmail, $subject, $content);

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
    public function delete(AvisRepository $avisRepository, EntityManagerInterface $entityManager, $avisId): \Symfony\Component\HttpFoundation\RedirectResponse
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
        MailerService          $mailerService,
        Request                $request,
        AvisRepository         $avisRepository,
        EntityManagerInterface $entityManager,
                               $avisId
    ): Response
    {
        $user = $this->getUser();
        $avis = $avisRepository->find($avisId);

        $currentNote=$avis->getNote();
        $currentCommentaire=$avis->getCommentaire();
        $nomPatient=$user->getNameUser();
        $professionalEmail=$avis->getProfessional()->getEmail();
        $professionalNom=$avis->getProfessional()->getNameUser();
        $subject = "Mise a jour Avis de " .$nomPatient;

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

            $newNote=$avis->getNote();
            $newCommentaire=$avis->getCommentaire();

            $content = "
                <p>Bonjour <strong>{$professionalNom}</strong>,</p>
                <p>Le patient <strong>{$nomPatient}</strong> a mis à jour son avis.</p>                             
                <p><strong>Ancien avis :</strong></p>
                <blockquote style='border-left: 4px solid #dc3545; padding-left: 10px; color: #333;'>
                    <p><strong>Note:</strong> {$currentNote}</p> 
                    <p><strong>Commentaire:</strong> {$currentCommentaire}</p>                    
                </blockquote>
                <p><strong>Nouveau avis :</strong></p>
                <blockquote style='border-left: 4px solid #28a745; padding-left: 10px; color: #333;'>
                    <p><strong>Note:</strong> {$newNote}</p> 
                    <p><strong>Commentaire:</strong> {$newCommentaire}</p>                    
                </blockquote>
                <p>Pour toute question, n'hésitez pas à contacter le patient ou votre équipe médicale.</p>
                <p>Cordialement,</p>
                <p><em>L'équipe Mediplus</em></p>
            ";

            $mailerService->sendEmail($professionalEmail, $subject, $content);


            $this->addFlash('success', 'Avis mis à jour avec succès.');
            return $this->redirectToRoute('app_avis_list');
        }

        return $this->render('avis/edit.html.twig', [
            'form' => $form->createView(),
            'avis' => $avis
        ]);
    }

    #[Route('/admin/avis', name: 'admin_avis_list')]
    public function listAvisAdmin(AvisRepository $avisRepository): Response
    {
        $avis = $avisRepository->findAll(); // Récupère tous les avis
        return $this->render('/avis/list.html.twig', [
            'avisList' => $avis,
        ]);
    }
//    #[Route('/admin/avis/{id}', name: 'admin_avis_details')]
//    public function detailsAvisAdmin(AvisRepository $avisRepository, $id): Response {
//    $avis = $avisRepository->find($id);
//
//    if (!$avis) {
//        throw $this->createNotFoundException("Avis non trouvé.");
//    }
//
//    return $this->render('/avis/backdetails.html.twig', [
//        'avis' => $avis,
//        'reponses' => $avis->getReponses(),
//    ]);
//}

    #[Route('/admin/avis/{id}', name: 'admin_avis_details')]
    public function showAvisDetails(Avis $avis, ReponseRepository $reponseRepository): Response
    {
        $reponses = $reponseRepository->findBy(['avis' => $avis]);

        return $this->render('avis/backdetails.html.twig', [
            'avis' => $avis,
            'reponses' => $reponses
        ]);
    }

//    statistiques




}
