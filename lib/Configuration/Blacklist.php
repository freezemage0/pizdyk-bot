<?php


namespace Freezemage\Pizdyk\Configuration;

final class Blacklist
{
    public array $canBeUserBy = [];

    public function __construct(array $canBeUserBy)
    {
        $this->canBeUserBy = $canBeUserBy;
    }
}