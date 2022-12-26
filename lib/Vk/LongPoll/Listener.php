<?php


namespace Freezemage\Pizdyk\Vk\LongPoll;

use Exception;
use Freezemage\Pizdyk\Configuration;
use Freezemage\Pizdyk\Vk\Message\Collection;
use Freezemage\Pizdyk\Vk\Message\Collection as MessageCollection;
use Freezemage\Pizdyk\Vk\Message\Item;
use RuntimeException;


final class Listener
{
    private Configuration $configuration;
    private Connection $connection;
    private ConnectionFactory $connectionFactory;
    private Client $lpClient;
    private int $attempts = 0;

    public function __construct(Configuration $configuration, ConnectionFactory $connectionFactory, Client $lpClient)
    {
        $this->configuration = $configuration;
        $this->connectionFactory = $connectionFactory;
        $this->lpClient = $lpClient;
    }

    public function listen(): MessageCollection
    {
        if ($this->attempts > $this->configuration->getApi()->getMaxLongPollAttempts()) {
            throw new Exception('Long poll server is unavailable, shutting down...'); // todo: custom exception
        }

        $collection = new Collection();

        $connection = $this->getConnection();
        try {
            $messages = $this->lpClient->fetch($connection);
        } catch (RuntimeException $e) {
            unset($this->connection);
            $this->attempts += 1;
            return $collection;
        }

        foreach ($messages as $message) {
            $collection->push(new Item($message['peer_id'], $message['from_id'], $message['text']));
        }

        return $collection;
    }

    private function getConnection(): Connection
    {
        if (!isset($this->connection)) {
            $this->connection = $this->connectionFactory->createConnection();
        }

        return $this->connection;
    }
}