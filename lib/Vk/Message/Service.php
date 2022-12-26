<?php


namespace Freezemage\Pizdyk\Vk\Message;


use Freezemage\Pizdyk\Vk\Client;


final class Service
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function send(Item $item): void
    {
        $message = ['peer_id' => $item->peerId, 'random_id' => mt_rand()];
        if (isset($item->text)) {
            $message['message'] = $item->text;
        }

        if (isset($item->attachments)) {
            $message['attachment'] = implode(',', $item->attachments);
        }

        $this->client->send('messages.send', $message);
    }
}