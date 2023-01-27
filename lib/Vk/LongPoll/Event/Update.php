<?php


namespace Freezemage\Pizdyk\Vk\LongPoll\Event;

use Freezemage\Pizdyk\Engine\Event;


interface Update extends Event
{
    public function getVersion(): string;
    public function getEventId(): string;

    public function getItem(): Updatable;
}