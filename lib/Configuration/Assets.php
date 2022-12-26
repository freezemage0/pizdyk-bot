<?php


namespace Freezemage\Pizdyk\Configuration;

final class Assets
{
    public array $generic;
    public string $aoe;

    public function __construct(array $generic, string $aoe)
    {
        $this->generic = $generic;
        $this->aoe = $aoe;
    }
}