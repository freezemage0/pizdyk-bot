<?php


namespace Freezemage\Pizdyk\Configuration\Assets;

final class Audios
{
    public array $forcedCensorship = [];

    public function __construct(array $forcedCensorship)
    {
        $this->forcedCensorship = $forcedCensorship;
    }
}