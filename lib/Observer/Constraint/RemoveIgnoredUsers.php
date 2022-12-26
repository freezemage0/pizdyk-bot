<?php


namespace Freezemage\Pizdyk\Observer\Constraint;

use Freezemage\Pizdyk\Filter\Constraint;
use Freezemage\Pizdyk\Filter\Filterable;


final class RemoveIgnoredUsers implements Constraint
{
    private array $ignoredUsers;

    public function __construct(array $ignoredUsers)
    {
        $this->ignoredUsers = $ignoredUsers;
    }

    public function evaluate(Filterable $item): bool
    {
        return !in_array($item->senderId, $this->ignoredUsers);
    }
}