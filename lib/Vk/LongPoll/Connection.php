<?php


namespace Freezemage\Pizdyk\Vk\LongPoll;

final class Connection
{
    public string $ts;
    public string $key;
    public string $server;

    public function __construct(string $ts, string $key, string $server)
    {
        $this->ts = $ts;
        $this->key = $key;
        $this->server = $server;
    }

}