<?php


namespace Freezemage\Pizdyk\Engine;

enum EventType: string
{
    case ON_TICK = 'tick';
    case ON_LONG_POLL = 'long-poll';
}