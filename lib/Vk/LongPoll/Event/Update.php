<?php


namespace Freezemage\Pizdyk\Vk\LongPoll\Event;

interface Update
{
    public function getVersion(): string;
    public function getEventId(): string;

    /**
     * @return Updatable can be redefined in implementors.
     */
    public function getEntity();
}