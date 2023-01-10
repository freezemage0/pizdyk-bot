<?php


namespace Freezemage\Pizdyk\Command\Constraint;

use Freezemage\Pizdyk\Filter\Constraint;
use Freezemage\Pizdyk\Filter\Filterable;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Type\NewMessage;


final class HasCommand implements Constraint
{
    /** @var array */
    private array $commands;
    public function evaluate(Filterable $item): bool
    {
        if (!($item instanceof NewMessage)) {
            return false;
        }


    }
}