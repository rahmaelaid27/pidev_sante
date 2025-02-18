<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HometesController extends AbstractController{
    #[Route('/ho', name: 'ho')]
    public function index(): Response
    {
        return $this->render('front/base.html.twig', [
            'controller_name' => 'HometesController',
        ]);
    }
    #[Route('/hoo', name: 'hoo')]
    public function index1(): Response
    {
        return $this->render('back/base.html.twig', [
            'controller_name' => 'HometesController',
        ]);
    }

}
