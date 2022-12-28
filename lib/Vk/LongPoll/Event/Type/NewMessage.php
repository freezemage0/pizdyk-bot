<?php


namespace Freezemage\Pizdyk\Vk\LongPoll\Event\Type;

use Freezemage\Pizdyk\Filter\Filterable;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Updatable;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Update;
use Freezemage\Pizdyk\Vk\Message\Item;
use UnexpectedValueException;


final class NewMessage implements Update, Filterable
{
    private Item $updatableItem;
    private string $eventId;
    private string $version;

    public function __construct(string $eventId, string $version, Item $updatableItem)
    {
        $this->updatableItem = $updatableItem;
        $this->eventId = $eventId;
        $this->version = $version;
    }

    public function getEntity(): Updatable
    {
        return $this->updatableItem;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function getEventId(): string
    {
        return $this->eventId;
    }
}