<?php


namespace Freezemage\Pizdyk\Statistics;

final class Item
{
    public ?int $id;
    public int $peerId;
    public string $name;
    public int $counter;

    public function __construct(?int $id, int $peerId, string $name, int $counter)
    {
        $this->id = $id;
        $this->peerId = $peerId;
        $this->name = $name;
        $this->counter = $counter;
    }
}