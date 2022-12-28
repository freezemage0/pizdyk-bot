<?php


namespace Freezemage\Pizdyk\Vk\LongPoll\Event;

use ArrayIterator;
use Freezemage\Pizdyk\Filter\Constraint;
use Iterator;


final class Collection implements Iterator
{
    /** @var array<Update> */
    private array $updates = [];
    private Iterator $iterator;

    public function push(Update $entity): void
    {
        $this->updates[] = $entity;
    }

    public function filter(Constraint $constraint): Collection
    {
        $collection = clone $this;
        $collection->updates = array_filter($collection->updates, [$constraint, 'evaluate']);

        return $collection;
    }

    public function current(): Update
    {
        return $this->iterator->current();
    }

    public function next(): void
    {
        $this->iterator->next();
    }

    public function key(): int
    {
        return $this->iterator->key();
    }

    public function valid(): bool
    {
        return $this->iterator->valid();
    }

    public function rewind(): void
    {
        $this->iterator = new ArrayIterator($this->updates);
    }
}