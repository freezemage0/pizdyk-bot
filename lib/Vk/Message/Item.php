<?php


namespace Freezemage\Pizdyk\Vk\Message;

use Freezemage\Pizdyk\Filter\Filterable;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Updatable;


final class Item implements Filterable, Updatable
{
    public const DIRECTION_IN = 0;
    public const DIRECTION_OUT = 1;

    public ?string $text;
    public string $peerId;
    public array $attachments;
    public ?int $senderId;
    public int $direction;
    public int $date;

    public function __construct(
            string $peerId,
            ?int $senderId,
            ?string $text,
            int $direction,
            int $date,
            array $attachments = []
    ) {
        $this->peerId = $peerId;
        $this->text = $text;
        $this->senderId = $senderId;
        $this->attachments = $attachments;
        $this->direction = $direction;
        $this->date = $date;
    }
}