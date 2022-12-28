<?php


namespace Freezemage\Pizdyk\Vk\User;

use Freezemage\Pizdyk\Vk\Client;


class Service
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function get(int $id): Item
    {
        $response = $this->client->send('users.get', ['user_ids' => $id, 'fields' => 'screen_name']);
        foreach ($response['response'] as $user) {
            if ($user['id'] != $id) {
                continue;
            }

            return new Item($id, $user['screen_name'] ?? null);
        }

        return new Item($id, null);
    }
}