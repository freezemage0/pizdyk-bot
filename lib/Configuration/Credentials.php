<?php


namespace Freezemage\Pizdyk\Configuration;

final class Credentials
{
    public string $communityId;
    public string $communityToken;

    public function __construct(string $communityId, string $communityToken)
    {
        $this->communityId = $communityId;
        $this->communityToken = $communityToken;
    }
}