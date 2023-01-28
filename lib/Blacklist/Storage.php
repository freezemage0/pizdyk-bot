<?php


namespace Freezemage\Pizdyk\Blacklist;

final class Storage
{
    private array $storage = [];

    public function add(int $peerId, int $userId): void
    {
        if ($this->has($peerId, $userId)) {
            return;
        }

        if (!isset($this->storage[$peerId])) {
            $this->storage[$peerId] = [];
        }

        $this->storage[$peerId][] = $userId;
    }

    public function delete(int $peerId, int $userId): void
    {
        if (!$this->has($peerId, $userId)) {
            return;
        }

        $index = array_search($userId, $this->storage[$peerId]);
        if ($index !== false) {
            unset($this->storage[$peerId][$index]);
        }
    }

    public function has(int $peerId, int $userId): bool
    {
        if (!isset($this->storage[$peerId])) {
            return false;
        }

        return in_array($userId, $this->storage[$peerId]);
    }

    public function get(int $peerId): array
    {
        return $this->storage[$peerId] ?? [];
    }
}