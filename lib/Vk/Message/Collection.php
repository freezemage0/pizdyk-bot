<?php


namespace Freezemage\Pizdyk\Vk\Message;


use ArrayIterator;
use Freezemage\Pizdyk\Filter\Constraint;
use Iterator;


/**
 * TODO: Коллекцию следует разделить на две: одна для входящих сообщений, вторая - для ответов.
 */
final class Collection implements Iterator
{
    /** @var array<Item> */
    private array $items;
    private ArrayIterator $iterator;

    public function __construct()
    {
        $this->items = array();
    }

    public function merge(Collection $collection): Collection
    {
        $merged = clone $this;
        $merged->items = array_merge($merged->items, $collection->items);

        return $merged;
    }

    public function push(Item $item): void
    {
        $this->items[] = $item;
    }

    public function filter(Constraint $constraint): Collection
    {
        $collection = clone $this;
        $collection->items = array_filter($collection->items, array($constraint, 'evaluate'));

        return $collection;
    }

    public function current(): Item
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
        $this->iterator = new ArrayIterator($this->items);
    }
}