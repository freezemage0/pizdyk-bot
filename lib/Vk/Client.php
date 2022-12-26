<?php


namespace Freezemage\Pizdyk\Vk;


use LogicException;
use Psr\Http\Client\ClientInterface;
use Psr\Http\Message\RequestFactoryInterface;
use Psr\Http\Message\StreamFactoryInterface;
use Psr\Http\Message\UriFactoryInterface;


final class Client
{
    private ClientInterface $client;
    private RequestFactoryInterface $requestFactory;
    private StreamFactoryInterface $streamFactory;
    private UriFactoryInterface $uriFactory;

    private string $token;
    private string $baseUri;
    private string $version;

    public function __construct(
            ClientInterface $client,
            RequestFactoryInterface $requestFactory,
            StreamFactoryInterface $streamFactory,
            UriFactoryInterface $uriFactory
    ) {
        $this->client = $client;
        $this->requestFactory = $requestFactory;
        $this->streamFactory = $streamFactory;
        $this->uriFactory = $uriFactory;
    }

    public function setAuthToken(string $token): void
    {
        $this->token = $token;
    }

    public function setBaseUri(string $baseUri): void
    {
        $this->baseUri = $baseUri;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function send(string $method, array $body): array
    {
        if (!isset($this->token, $this->baseUri)) {
            throw new LogicException('Unable to send request: missing required parameters.');
        }
        $body['access_token'] = $this->token;

        $method = trim($method, '/');
        $uri = $this->uriFactory->createUri("{$this->baseUri}/{$method}");

        if (isset($this->version)) {
            $body['v'] = $this->version;
        }

        $request = $this->requestFactory->createRequest('POST', $uri);
        $request = $request->withBody($this->streamFactory->createStream(http_build_query($body)));

        $response = $this->client->sendRequest($request);
        return json_decode($response->getBody()->getContents(), true);
    }
}