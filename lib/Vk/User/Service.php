<?php


namespace Freezemage\Pizdyk\Vk\User;

use Freezemage\Pizdyk\Vk\Client;


class Service
{
    private Client $client;
    private array $cache = [];

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @return Item[]
     */
    public function get(int ...$id): array
    {
        $users = [];
        foreach ($id as $index => $user) {
            if (isset($this->cache[$user])) {
                $users[$user] = $this->cache[$user];
                unset($id[$index]);
            }
        }

        if (empty($id)) {
            return $users;
        }

        $response = $this->client->send('users.get', ['user_ids' => $id, 'fields' => 'screen_name']);
        foreach ($response['response'] as $user) {
            $this->cache[$user['id']] = $users[$user['id']] = new Item(
                    $user['id'],
                    $user['screen_name'] ?? null,
                    $user['first_name'] ?? null,
                    $user['last_name'] ?? null
            );
        }
        return $users;
    }
}