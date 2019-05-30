<?php

namespace App\EventListener;

use App\Event\SubscriptionCreatedEvent;
use App\Event\SubscriptionDeletedEvent;
use App\Utils\Notificator;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

class MailchimpListener
{
    protected $notificator;

    protected $apiKey;

    protected $url;

    protected $listId;

    protected $client;

    public function __construct(Notificator $notificator)
    {
        $this->notificator = $notificator;
        $this->apiKey      = getenv('MAILCHIMP_API_KEY');
        $this->url         = getenv('MAILCHIMP_API_URL');
        $this->listId      = getenv('MAILCHIMP_LIST_ID');
        $this->client      = new Client();
    }

    public function onSubscriptionCreate(SubscriptionCreatedEvent $event)
    {
        if (! $this->apiKey) {
            return;
        }

        $subscription = $event->getSubscription();

        try {
            $this->client->post($this->url . "/lists/" . $this->listId . "/members", [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("user:" . $this->apiKey),
                    'Content-Type'  => 'application/json',
                ],
                'json'    => [
                    'status'        => 'subscribed',
                    'name'          => $subscription->getName(),
                    'email_address' => $subscription->getMail(),
                ],
            ]);
        } catch (ClientException $exception) {
            $this->notificator->error('Mailchimp integration error', $exception->getMessage());
        }
    }

    public function onSubscriptionDelete(SubscriptionDeletedEvent $event)
    {
        if (! $this->apiKey) {
            return;
        }

        $subscription   = $event->getSubscription();
        $subscriberHash = md5($subscription->getMail());

        try {
            $this->client->delete($this->url . "/lists/" . $this->listId . "/members/$subscriberHash", [
                'headers' => [
                    'Authorization' => 'Basic ' . base64_encode("user:" . $this->apiKey),
                    'Content-Type'  => 'application/json',
                ],
            ]);
        } catch (ClientException $exception) {
            $this->notificator->error('Mailchimp integration error', $exception->getMessage());
        }
    }
}
