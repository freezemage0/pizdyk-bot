<?php


namespace Freezemage\Pizdyk\Statistics\Top;

final class Item
{
    public int $userId;
    public int $peerId;
    public int $counter;

    public function __construct(int $userId, int $peerId, int $counter)
    {
        $this->userId = $userId;
        $this->peerId = $peerId;
        $this->counter = $counter;
    }
}