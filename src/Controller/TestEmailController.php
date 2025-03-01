<?php

namespace App\Controller;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class TestEmailController extends AbstractController
{
    #[Route('/test-email', name: 'test_email')]
    public function testEmail(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('jmalyessine114@gmail.com')
            ->to('yessinecss90@gmail.com')
            ->subject('Test d\'envoi d\'email depuis Symfony')
            ->text('Ceci est un email de test.');

        try {
            $mailer->send($email);
            return new Response('Email envoyé avec succès !');
        } catch (\Exception $e) {
            return new Response('Erreur lors de l\'envoi : ' . $e->getMessage());
        }
    }
}
