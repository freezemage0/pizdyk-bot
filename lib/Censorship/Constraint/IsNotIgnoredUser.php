<?php


namespace Freezemage\Pizdyk\Censorship\Constraint;

use Freezemage\Pizdyk\Filter\Constraint;
use Freezemage\Pizdyk\Filter\Filterable;
use Freezemage\Pizdyk\Vk\LongPoll\Event\Type\NewMessage;
use Freezemage\Pizdyk\Vk\Message\Item;


final class IsNotIgnoredUser implements Constraint
{
    private array $ignoredUsers;

    public function __construct(array $ignoredUsers)
    {
        $this->ignoredUsers = $ignoredUsers;
    }

    public function evaluate(Filterable $item): bool
    {
        if (!($item instanceof NewMessage)) {
            return false;
        }

        return !in_array($item->getEntity()->senderId, $this->ignoredUsers);
    }
}