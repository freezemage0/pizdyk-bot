<?php


namespace Freezemage\Pizdyk\Vk\LongPoll;

use Freezemage\Pizdyk\Vk\LongPoll\Event\Collection;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Factory as EventFactory;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use RuntimeException;


final class Client
{
    private ClientInterface $client;
    private RequestFactoryInterface $requestFactory;
    private UriFactoryInterface $uriFactory;
    private EventFactory $eventFactory;

    public function __construct(
            ClientInterface $client,
            RequestFactoryInterface $requestFactory,
            UriFactoryInterface $uriFactory,
            EventFactory $eventFactory
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
        $this->eventFactory = $eventFactory;
    }

    public function fetch(Connection $connection, int $wait = 25, int $mode = 2, int $version = 3): Collection
    {
        $uri = $this->uriFactory->createUri($connection->server);
        $parameters = [
                'act' => 'a_check',
                'key' => $connection->key,
                'ts' => $connection->ts,
                'wait' => $wait,
                'mode' => $mode,
                'version' => $version
        ];

        $uri = $uri->withQuery(http_build_query($parameters));
        $request = $this->requestFactory->createRequest('GET', $uri);
        $response = $this->client->sendRequest($request);

        $body = json_decode($response->getBody()->getContents(), true);

        if (isset($body['ts'])) {
            $connection->ts = $body['ts'];
        }

        if (isset($body['failed']) && $body['failed'] > 1) {
            throw new RuntimeException('Long poll failed, create another connection and retry.');
        }

        $collection = new Collection();

        foreach ($body['updates'] as $update) {
            $collection->push($this->eventFactory->create($update['type'], $update['event_id'], $update['v'], $update['object']));
        }

        return $collection;
    }
}