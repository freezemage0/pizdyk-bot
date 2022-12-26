<?php


namespace Freezemage\Pizdyk\Vk\LongPoll\Factory;

use Freezemage\Pizdyk\Vk\Client;
use Freezemage\Pizdyk\Vk\LongPoll\Connection;
use Freezemage\Pizdyk\Vk\LongPoll\ConnectionFactory;


final class Group implements ConnectionFactory
{
    private Client $client;
    private string $groupId;

    public function __construct(Client $client, string $groupId)
    {
        $this->client = $client;
        $this->groupId = $groupId;
    }

    public function createConnection(): Connection
    {
        $response = $this->client->send('groups.getLongPollServer', ['group_id' => $this->groupId]);
        $connection = $response['response'];

        return new Connection(
                $connection['ts'],
                $connection['key'],
                $connection['server']
        );
    }
}