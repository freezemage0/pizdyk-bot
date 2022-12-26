<?php


namespace Freezemage\Pizdyk\Vk\LongPoll;

use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;
use RuntimeException;


final class Client
{
    private const DIRECTION_IN = 0;
    private ClientInterface $client;
    private RequestFactoryInterface $requestFactory;
    private UriFactoryInterface $uriFactory;

    public function __construct(
            ClientInterface $client,
            RequestFactoryInterface $requestFactory,
            UriFactoryInterface $uriFactory
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->uriFactory = $uriFactory;
    }

    public function fetch(Connection $connection, int $wait = 25, int $mode = 2, int $version = 3): array
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

        $candidates = [];

        foreach ($body['updates'] as $update) {
            if ($update['type'] != 'message_new') {
                continue; // drop any event that is not "message_new"
            }

            $message = $update['object']['message'];
            if ($message['out'] != Client::DIRECTION_IN) {
                continue; // drop all non-incoming messages
            }

            $candidates[] = $message;
        }

        return $candidates;
    }
}