<?php


namespace Freezemage\Pizdyk\Statistics;


use SQLite3;


final class Repository
{
    private SQLite3 $driver;

    public function __construct(SQLite3 $driver)
    {
        $this->driver = $driver;
    }

    public function findByName(int $peerId, string $name): ?Item
    {
        $result = $this->driver->query("SELECT rowid, * FROM statistics WHERE peerId = {$peerId} AND name = '{$name}';");
        if (empty($result)) {
            return null;
        }

        $item = $result->fetchArray(SQLITE3_ASSOC);
        return !empty($item) ? new Item($item['rowid'], $item['peerId'], $item['name'], $item['counter']) : null;
    }

    /**
     * @return Item[]
     */
    public function find(int $peerId): array
    {
        $result = $this->driver->query("SELECT rowid, * FROM statistics WHERE peerId = {$peerId};");

        $items = [];
        while ($item = $result->fetchArray(SQLITE3_ASSOC)) {
            $items[] = new Item(
                    $item['rowid'],
                    $item['peerId'],
                    $item['name'],
                    $item['counter']
            );
        }

        return $items;
    }

    public function add(Item $item): void
    {
        $this->driver->query(
                "INSERT INTO statistics (peerId, name, counter) VALUES ('{$item->peerId}', '{$item->name}', '{$item->counter}');"
        );

        $item->id = $this->driver->lastInsertRowID();
    }

    public function update(Item $item): void
    {
        $this->driver->query(
                "UPDATE statistics SET peerId = {$item->peerId}, name = '{$item->name}', counter = {$item->counter} WHERE rowid = {$item->id};"
        );
    }
}