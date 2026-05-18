<?php

namespace App\Core;

use Symfony\Component\Mailer\Mailer as SymfonyMailer;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mime\Email;

class Mailer
{
    private SymfonyMailer $mailer;

    public function __construct()
    {
        $transport = Transport::fromDsn($_ENV['MAILER_DSN']);
        $this->mailer = new SymfonyMailer($transport);
    }

    public function send(string $to, string $subject, string $html): void
    {
        $email = (new Email())
            ->from($_ENV['MAIL_FROM'])
            ->to($to)
            ->subject($subject)
            ->html($html);

        $this->mailer->send($email);
    }
}