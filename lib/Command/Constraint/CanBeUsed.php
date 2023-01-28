<?php


namespace Freezemage\Pizdyk\Command\Constraint;

use Freezemage\Pizdyk\Filter\Constraint;
use Freezemage\Pizdyk\Filter\Filterable;
use Freezemage\Pizdyk\Vk\Message\Item;


final class CanBeUsed implements Constraint
{
    private array $whiteList;

    public function __construct(array $whiteList)
    {
        $this->whiteList = $whiteList;
    }

    public function evaluate(Filterable $item): bool
    {
        if (!($item instanceof Item)) {
            return false;
        }

        return in_array($item->senderId, $this->whiteList);
    }
}