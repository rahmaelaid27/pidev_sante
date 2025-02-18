<?php

namespace App\Controller;

use App\Entity\Blocs;
use App\Form\BlocsType;
use App\Repository\BlocsRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class BlocController extends AbstractController
{
    #[Route('/blocs', name: 'bloc.index')]
    public function index(BlocsRepository $repository): Response
    {
        $block = $repository->findAll();
        return $this->render('bloc/index.html.twig', [
            'blocs' => $block,
        ]);
    }

    #[Route('/bloc/add', name: 'bloc.create')]
    public function create(Request $request,BlocsRepository $repository,EntityManagerInterface $em): Response
    {
        $block = new Blocs();
        $form = $this->createForm(BlocsType::class, $block);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($block);
            $em->flush();
            return $this->redirectToRoute('bloc.index');
        }
        return $this->render('bloc/create.html.twig', [
            'bloc' => $block,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/bloc/{id}/edit', name: 'bloc.edit', requirements: ['id'=>'\d+'])]
    public function edit(Request $request, Blocs $bloc,EntityManagerInterface $em): Response
    {
        $form = $this->createForm(BlocsType::class, $bloc);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('bloc.index');
        }
        return $this->render('bloc/edit.html.twig', [
            'bloc' => $bloc,
            'form' => $form->createView(),
        ]);
    }

    #[Route('bloc/{id}/delete', name: 'bloc.delete', requirements: ['id'=>'\d+'])]
    public function delete(Request $request, Blocs $bloc,EntityManagerInterface $em): Response
    {
        $em->remove($bloc);
        $em->flush();
        return $this->redirectToRoute('bloc.index');
    }

}
