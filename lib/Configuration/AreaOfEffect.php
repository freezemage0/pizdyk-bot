<?php


namespace Freezemage\Pizdyk\Configuration;

class AreaOfEffect
{
    public int $maxProcCount;
    public int $maxPeriod; // in seconds

    public function __construct(int $maxProcCount, int $maxPeriod)
    {
        $this->maxProcCount = $maxProcCount;
        $this->maxPeriod = $maxPeriod * 60;
    }
}