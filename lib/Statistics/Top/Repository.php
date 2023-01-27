<?php


namespace Freezemage\Pizdyk\Statistics\Top;

use SQLite3;


final class Repository
{
    private SQLite3 $driver;

    public function __construct(SQLite3 $driver)
    {
        $this->driver = $driver;
    }

    /**
     * @return Item[]
     */
    public function find(int $peerId, int $limit): array
    {
        $result = $this->driver->query("SELECT * FROM statistics_top WHERE peerId = {$peerId} ORDER BY counter DESC LIMIT {$limit};");

        $items = [];
        while ($item = $result->fetchArray(SQLITE3_ASSOC)) {
            $items[] = new Item($item['userId'], $item['peerId'], $item['counter']);
        }

        return $items;
    }

    public function count(int $peerId): int
    {
        $result = $this->driver->query("SELECT sum(counter) as CNT FROM statistics_top WHERE peerId = {$peerId};");
        $count = $result->fetchArray(SQLITE3_ASSOC);

        return $count['CNT'];
    }

    public function findOne(int $peerId, int $userId): ?Item
    {
        $result = $this->driver->query("SELECT * FROM statistics_top WHERE userId = {$userId} AND peerId = {$peerId};");
        if (empty($result)) {
            return null;
        }
        $row = $result->fetchArray(SQLITE3_ASSOC);
        return !empty($row) ? new Item($row['userId'], $row['peerId'], $row['counter']) : null;
    }

    public function add(Item $item): void
    {
        $this->driver->query("INSERT INTO statistics_top (userId, peerId, counter) VALUES ('{$item->userId}', '{$item->peerId}', '{$item->counter}');");
    }

    public function update(Item $item): void
    {
        $this->driver->query("UPDATE statistics_top SET counter = {$item->counter} WHERE userId = {$item->userId} AND peerId = {$item->peerId};");
    }
}