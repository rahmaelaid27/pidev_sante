<?php

namespace App\Controller;

use App\Entity\Blocs;
use App\Form\SpecialitesType;
use App\Repository\BlocsRepository;
use App\Repository\SpecialitesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Entity\Specialites;

final class SpecialiteController extends AbstractController
{
    #[Route('admin/specialites', name: 'specialite.index')]
    public function index(SpecialitesRepository $repository,BlocsRepository $blocsRepository): Response
    {
        $specialites = $repository->findAll();
        $blocs = $blocsRepository->findAll();
        return $this->render('specialite/index.html.twig', [
            'specialites' => $specialites,
            'blocs' => $blocs,
        ]);
    }
    #[Route('/specialites', name: 'front.specialite.index')]
    public function indexfront(SpecialitesRepository $repository,BlocsRepository $blocsRepository): Response
    {
        $specialites = $repository->findAll();
        $blocs = $blocsRepository->findAll();
        return $this->render('specialite/front.html.twig', [
            'specialites' => $specialites,
            'blocs' => $blocs,
        ]);
    }
    #[Route('admin/specialite/add', name: 'specialite.create')]
    public function create(Request $request, SpecialitesRepository $repository, EntityManagerInterface $em): Response
    {
        $specialite = new Specialites();
        $form = $this->createForm(SpecialitesType::class, $specialite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($specialite);
            $em->flush();
            return $this->redirectToRoute('specialite.index');
        }
        return $this->render('specialite/create.html.twig', [
            'specialite' => $specialite,
            'form' => $form->createView(),
        ]);
    }
    #[Route('admin/specialite/{id}/edit', name: 'specialite.edit', requirements: ['id'=>'\d+'])]
    public function edit(Request $request, Specialites $specialite, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(SpecialitesType::class, $specialite);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('specialite.index');
        }
        return $this->render('specialite/edit.html.twig', [
            'specialite' => $specialite,
            'form' => $form->createView(),
        ]);
    }

    #[Route('admin/specialite/{id}/delete', name: 'specialite.delete', requirements: ['id'=>'\d+'])]
    public function delete(Request $request, Specialites $specialite, EntityManagerInterface $em): Response
    {
        $em->remove($specialite);
        $em->flush();
        return $this->redirectToRoute('specialite.index');
    }

}
