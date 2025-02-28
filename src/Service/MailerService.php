<?php

namespace App\Service;

use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Mime\Email;
use Symfony\Component\Mime\Part\DataPart;

class MailerService
{
    public function __construct(private MailerInterface $mailer) {}

    public function sendEmail(
        $to="",
        $subject="",
        $content=""
    ): void
    {
        $email = (new Email())
            ->from(new Address('Mediplus.support@gmail.com', 'Mediplus Support'))
            ->to($to)
            ->subject($subject)
            ->html($content);

            $this->mailer->send($email);
    }
}
