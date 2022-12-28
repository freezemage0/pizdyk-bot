<?php


namespace Freezemage\Pizdyk\Vk\LongPoll\Event;

interface Update
{
    public function getVersion(): string;
    public function getEventId(): string;
    public function getEntity(): Updatable;
}