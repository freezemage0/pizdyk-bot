<?php


namespace Freezemage\Pizdyk\Vk\LongPoll;

use Freezemage\Pizdyk\Configuration;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Collection;
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

    public function listen(): Collection
    {
        if ($this->attempts > $this->configuration->getApi()->maxLongPollAttempts) {
            throw new LongPollAttemptsExceededException('Long poll server is unavailable, shutting down...');
        }

        try {
            $connection = $this->getConnection();
            return $this->lpClient->fetch($connection);
        } catch (RuntimeException $e) {
            unset($this->connection);
            $this->attempts += 1;

            return $this->listen();
        }

    }

    private function getConnection(): Connection
    {
        if (!isset($this->connection)) {
            $this->connection = $this->connectionFactory->createConnection();
        }

        return $this->connection;
    }
}