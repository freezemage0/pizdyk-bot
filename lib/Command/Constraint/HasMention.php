<?php


namespace Freezemage\Pizdyk\Command\Constraint;

use Freezemage\Pizdyk\Filter\Constraint;
use Freezemage\Pizdyk\Filter\Filterable;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Type\NewMessage;


final class HasMention implements Constraint
{
    private array $prefixes;

    public function __construct(array $prefixes)
    {
        $this->prefixes = $prefixes;
    }

    public function evaluate(Filterable $item): bool
    {
        if (!($item instanceof NewMessage)) {
            return false;
        }

        $prefixes = implode('|', $this->prefixes);
        $pattern = "/{$prefixes}\s+/ui";

        return preg_match($pattern, $item->getItem()->text);
    }
}