<?php


namespace Freezemage\Pizdyk\Configuration\Assets;

final class Photos
{
    public array $generic = [];
    public array $aoe = [];
    public array $forcedCensorship = [];

    public function __construct(array $generic, array $aoe, array $forcedCensorship)
    {
        $this->generic = $generic;
        $this->aoe = $aoe;
        $this->forcedCensorship = $forcedCensorship;
    }
}