<?php


namespace Freezemage\Pizdyk\Engine\Event;

use Freezemage\Pizdyk\Engine\Event;

class Tick implements Event
{
    public function getItem(): string
    {
        return (string) time();
    }
}