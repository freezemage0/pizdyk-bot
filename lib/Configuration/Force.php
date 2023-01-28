<?php


namespace Freezemage\Pizdyk\Configuration;

final class Force
{
    public array $canBeUsedBy;

    public function __construct(array $canBeUsedBy)
    {
        $this->canBeUsedBy = $canBeUsedBy;
    }
}