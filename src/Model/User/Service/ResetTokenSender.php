<?php

declare(strict_types=1);

namespace App\Model\User\Service;

use App\Model\User\Entity\User\Email;
use App\Model\User\Entity\User\ResetToken;
use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class ResetTokenSender
{
    private $mailer;
    private $twig;

    private string $robotEmail;

    public function __construct(MailerInterface $mailer, Environment $twig, string $robotEmail)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
        $this->robotEmail = $robotEmail;
    }

    public function send(Email $email, ResetToken $token): void
    {
        try {
            $this->mailer->send(
                (new NotificationEmail())
                    ->subject('Password resetting')
                    ->htmlTemplate('mail/user/reset.html.twig')
                    ->context(['token' => $token->getToken()])
                    ->from($this->robotEmail)
                    ->to($email->getValue())
            );
        } catch (TransportExceptionInterface $e) {
            throw new \RuntimeException('Unable to send message.');
        }
    }
}
