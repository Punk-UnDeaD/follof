<?php

declare(strict_types=1);

namespace App\Service;

use Symfony\Bridge\Twig\Mime\NotificationEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class AsteriskNotificator
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

    public function poolUpdate($pool)
    {
        $this->mailer->send(
            (new NotificationEmail())
                ->subject('Asterisk pool update'.count($pool['accounts']))
                ->htmlTemplate('mail/json.html.twig')
                ->context(['data' => $pool])
                ->from($this->robotEmail)
                ->to($this->robotEmail)
        );
    }
}
