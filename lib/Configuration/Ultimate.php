<?php


namespace Freezemage\Pizdyk\Configuration;

final class Ultimate
{
    public int $userId;
    public string $description;
    public array $assets;

    public function __construct(int $userId, string $text, array $assets)
    {
        $this->userId = $userId;
        $this->description = $text;
        $this->assets = $assets;
    }
}