<?php


namespace Freezemage\Pizdyk\Vk\Message;

use Freezemage\Pizdyk\Filter\Filterable;

final class Item implements Filterable
{
    public ?string $text;
    public string $peerId;
    public array $attachments;
    public ?int $senderId;

    public function __construct(string $peerId, ?int $senderId, ?string $text, array $attachments = [])
    {
        $this->peerId = $peerId;
        $this->text = $text;
        $this->senderId = $senderId;
        $this->attachments = $attachments;
    }
}