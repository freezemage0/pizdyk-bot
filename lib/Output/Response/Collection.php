<?php


namespace Freezemage\Pizdyk\Output\Response;


use ArrayIterator;
use Freezemage\Pizdyk\Filter\Constraint;
use Freezemage\Pizdyk\Output\Response;
use Iterator;


final class Collection implements Iterator
{
    /** @var array<Response> */
    private array $responses = [];
    private Iterator $iterator;

    public function push(Response $response): void
    {
        $this->responses[] = $response;
    }

    public function filter(Constraint $constraint): Collection
    {
        throw new \LogicException('Not implemented.');
    }

    public function merge(Collection $collection): Collection
    {
        $merged = clone $this;
        foreach ($collection as $response) {
            $merged->push($response);
        }

        return $merged;
    }

    public function current(): Response
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
        $this->iterator = new ArrayIterator($this->responses);
    }
}