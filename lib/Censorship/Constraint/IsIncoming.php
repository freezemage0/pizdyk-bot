<?php


namespace Freezemage\Pizdyk\Censorship\Constraint;

use Freezemage\Pizdyk\Filter\Constraint;
use Freezemage\Pizdyk\Filter\Filterable;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Type\NewMessage;
use Freezemage\Pizdyk\Vk\Message\Item;


final class IsIncoming implements Constraint
{
    public function evaluate(Filterable $item): bool
    {
        if (!($item instanceof NewMessage)) {
            return false;
        }

        return $item->getEntity()->direction == Item::DIRECTION_IN;
    }
}