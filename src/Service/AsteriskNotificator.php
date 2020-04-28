<?php

declare(strict_types=1);

namespace App\Service;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Mailer\MailerInterface;

class AsteriskNotificator
{
    private MailerInterface $mailer;
    private string $robotEmail;
    private string $asteriskApiUrl;
    private Client $client;

    public function __construct(MailerInterface $mailer, string $robotEmail, string $asteriskApiUrl, Client $client)
    {
        $this->mailer = $mailer;
        $this->robotEmail = $robotEmail;
        $this->asteriskApiUrl = $asteriskApiUrl;
        $this->client = $client;
    }

    public function poolUpdate($pool)
    {
        $this->client->post(
            $this->asteriskApiUrl,
            [
                RequestOptions::JSON => $pool,
            ]
        );
    }
}
