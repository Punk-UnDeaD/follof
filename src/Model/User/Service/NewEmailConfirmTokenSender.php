<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class NewEmailConfirmTokenSender
{
    private MailerInterface $mailer;
    private Environment $twig;
    private string $robotEmail;

    public function __construct(MailerInterface $mailer, Environment $twig, string $robotEmail)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->robotEmail = $robotEmail;
    }

    /**
     * @param Email $email
     * @param string $token
     */
    public function send(Email $email, string $token): void
    {
        try {
            $this->mailer->send(
                (new NotificationEmail())
                    ->subject('Email Confirmation')
                    ->htmlTemplate('mail/user/email.html.twig')
                    ->context(['token' => $token])
                    ->from($this->robotEmail)
                    ->to($email->getValue())
            );
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Unable to send message.');
        }
    }
}
