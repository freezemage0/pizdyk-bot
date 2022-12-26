<?php


namespace Freezemage\Pizdyk\Configuration;

final class Api
{
    public string $baseUri;
    public string $version;
    public int $maxLongPollAttempts;

    public function __construct(string $baseUri, string $version, int $maxLongPollAttempts = 5)
    {
        $this->baseUri = $baseUri;
        $this->version = $version;
        $this->maxLongPollAttempts = $maxLongPollAttempts;
    }
}