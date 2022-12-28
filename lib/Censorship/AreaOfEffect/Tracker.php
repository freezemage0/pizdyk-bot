<?php


namespace Freezemage\Pizdyk\Censorship\AreaOfEffect;

use Freezemage\Pizdyk\Configuration\AreaOfEffect;
use Freezemage\Pizdyk\Vk\Message\Item;


final class Tracker
{
    /**
     * @var array<array<Item>>
     */
    private array $trackedItems = [];

    private AreaOfEffect $config;

    public function __construct(AreaOfEffect $config)
    {
        $this->config = $config;
    }

    public function push(Item $item): void
    {
        if (!isset($this->trackedItems[$item->peerId])) {
            $this->trackedItems[$item->peerId] = [];
        }

        $this->trackedItems[$item->peerId][] = $item;

        $timestamp = time();
        foreach ($this->trackedItems[$item->peerId] as $index => $item) {
            if ($timestamp - $item->date > $this->config->maxPeriod) {
                unset($this->trackedItems[$item->peerId][$index]);
            }
        }
    }

    public function getExceedingPeers(): array
    {
        $peers = [];

        foreach ($this->trackedItems as $peerId => $items) {
            $counter = count($items);
            if ($counter >= $this->config->maxProcCount) {
                $peers[] = $peerId;
            }
        }

        return $peers;
    }

    public function clear(string $peerId): void
    {
        $this->trackedItems[$peerId] = [];
    }
}