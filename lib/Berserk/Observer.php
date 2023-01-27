<?php


namespace Freezemage\Pizdyk\Berserk;

use Freezemage\Pizdyk\ObserverInterface;
use Freezemage\Pizdyk\Output\Response\Collection as ResponseCollection;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Collection as EventCollection;


final class Observer implements ObserverInterface
{
    private array $peers;
    private int $frequency;

    public function addPeer(string $peerId): void
    {
        $this->peers[] = [
                'peer' => $peerId,
                'next' => time(),
                'counter' => 0
        ];
    }

    public function update(EventCollection $collection): ResponseCollection
    {
        return new ResponseCollection();
    }
}